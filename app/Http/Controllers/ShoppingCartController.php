<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShoppingCartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index()
    {
        $cartItems = ShoppingCart::with('product.category')
            ->where('user_id', auth()->id())
            ->get();

        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->unit_price;
        });

        return Inertia::render('cart/index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
        ]);
    }

    /**
     * Add item to shopping cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Check stock availability
        if ($product->stock_quantity < $request->quantity) {
            return back()->with('error', 'Insufficient stock available.');
        }

        // Calculate price based on user's reseller level
        $unitPrice = $product->base_price;
        if (auth()->user()->resellerLevel) {
            $unitPrice = $product->getPriceForResellerLevel(auth()->user()->resellerLevel);
        }

        // Check if item already exists in cart
        $existingItem = ShoppingCart::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $request->quantity;
            
            // Check total quantity against stock
            if ($product->stock_quantity < $newQuantity) {
                return back()->with('error', 'Cannot add more items. Insufficient stock.');
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'unit_price' => $unitPrice, // Update price in case reseller level changed
            ]);
        } else {
            ShoppingCart::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        return back()->with('success', 'Item added to cart successfully.');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, ShoppingCart $shoppingCart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Verify ownership
        if ($shoppingCart->user_id !== auth()->id()) {
            abort(403);
        }

        // Check stock availability
        if ($shoppingCart->product->stock_quantity < $request->quantity) {
            return back()->with('error', 'Insufficient stock available.');
        }

        $shoppingCart->update([
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove item from cart.
     */
    public function destroy(ShoppingCart $shoppingCart)
    {
        // Verify ownership
        if ($shoppingCart->user_id !== auth()->id()) {
            abort(403);
        }

        $shoppingCart->delete();

        return back()->with('success', 'Item removed from cart.');
    }


}