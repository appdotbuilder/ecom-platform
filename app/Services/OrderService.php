<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new OrderService instance.
     *
     * @param  CommissionService  $commissionService
     * @return void
     */
    public function __construct(
        private CommissionService $commissionService
    ) {}

    /**
     * Create an order from shopping cart.
     *
     * @param  User  $user
     * @param  array  $orderData
     * @return Order
     */
    public function createOrderFromCart(User $user, array $orderData): Order
    {
        return DB::transaction(function () use ($user, $orderData) {
            $cartItems = ShoppingCart::with('product')
                ->where('user_id', $user->id)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty.');
            }

            // Calculate totals
            $subtotal = $cartItems->sum(function($item) {
                return $item->quantity * $item->unit_price;
            });

            $shippingCost = $orderData['shipping_cost'] ?? 0;
            $taxAmount = $orderData['tax_amount'] ?? 0;
            $discountAmount = $orderData['discount_amount'] ?? 0;
            $totalAmount = $subtotal + $shippingCost + $taxAmount - $discountAmount;

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'reseller_id' => $orderData['reseller_id'] ?? null,
                'order_type' => $orderData['order_type'] ?? 'online',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $orderData['payment_method'] ?? null,
                'shipping_address' => $orderData['shipping_address'],
                'billing_address' => $orderData['billing_address'] ?? null,
                'shipping_service' => $orderData['shipping_service'] ?? null,
                'shipping_service_type' => $orderData['shipping_service_type'] ?? null,
                'notes' => $orderData['notes'] ?? null,
            ]);

            // Create order items and update stock
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                
                // Check stock availability
                if ($product->stock_quantity < $cartItem->quantity) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'total_price' => $cartItem->quantity * $cartItem->unit_price,
                    'product_options' => $cartItem->product_options,
                ]);

                // Update product stock
                $product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Clear cart
            $cartItems->each->delete();

            // Calculate commissions if order is confirmed
            if ($order->status === 'confirmed' || $order->payment_status === 'paid') {
                $this->commissionService->calculateCommissions($order);
            }

            return $order;
        });
    }

    /**
     * Create a POS order.
     *
     * @param  array  $orderData
     * @param  array  $items
     * @return Order
     */
    public function createPosOrder(array $orderData, array $items): Order
    {
        return DB::transaction(function () use ($orderData, $items) {
            $subtotal = 0;

            // Validate items and calculate subtotal
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Product not found: {$item['product_id']}");
                }

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $orderData['customer_id'],
                'reseller_id' => $orderData['reseller_id'],
                'order_type' => 'pos',
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'tax_amount' => $orderData['tax_amount'] ?? 0,
                'discount_amount' => $orderData['discount_amount'] ?? 0,
                'total_amount' => $subtotal + ($orderData['tax_amount'] ?? 0) - ($orderData['discount_amount'] ?? 0),
                'status' => 'confirmed',
                'payment_status' => $orderData['payment_method'] === 'hutang' ? 'hutang' : 'paid',
                'payment_method' => $orderData['payment_method'],
                'shipping_address' => $orderData['customer_address'] ?? ['address' => 'In-store pickup'],
                'notes' => $orderData['notes'] ?? null,
            ]);

            // Create order items and update stock
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                // Update product stock
                $product->decrement('stock_quantity', $item['quantity']);
            }

            // Calculate commissions
            $this->commissionService->calculateCommissions($order);

            // Update user sales statistics
            $customer = User::find($orderData['customer_id']);
            if ($customer) {
                $customer->increment('total_sales', $order->total_amount);
            }

            return $order;
        });
    }

    /**
     * Update order status.
     *
     * @param  Order  $order
     * @param  string  $status
     * @param  array  $additionalData
     * @return bool
     */
    public function updateOrderStatus(Order $order, string $status, array $additionalData = []): bool
    {
        $updateData = ['status' => $status];
        
        switch ($status) {
            case 'shipped':
                $updateData['shipped_at'] = now();
                if (isset($additionalData['tracking_number'])) {
                    $updateData['tracking_number'] = $additionalData['tracking_number'];
                }
                break;
                
            case 'delivered':
                $updateData['delivered_at'] = now();
                break;
                
            case 'cancelled':
                // Restore stock quantities
                foreach ($order->items as $item) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
                
                // Cancel pending commissions
                $order->commissions()->where('status', 'pending')->update(['status' => 'cancelled']);
                break;
        }

        return $order->update($updateData);
    }

    /**
     * Process payment for an order.
     *
     * @param  Order  $order
     * @param  string  $paymentMethod
     * @param  array  $paymentData
     * @return bool
     */
    public function processPayment(Order $order, string $paymentMethod, array $paymentData = []): bool
    {
        $order->update([
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'payment_data' => $paymentData,
        ]);

        // Confirm order if still pending
        if ($order->status === 'pending') {
            $order->update(['status' => 'confirmed']);
        }

        // Calculate commissions if not already calculated
        if ($order->commissions()->count() === 0) {
            $this->commissionService->calculateCommissions($order);
        }

        // Update user sales statistics
        $order->user->increment('total_sales', $order->total_amount);

        return true;
    }
}