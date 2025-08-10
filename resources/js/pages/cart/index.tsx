import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { ShoppingCart, Plus, Minus, Trash2, Package } from 'lucide-react';

interface Product {
    id: number;
    name: string;
    slug: string;
    images: string[];
    category: {
        name: string;
    };
}

interface CartItem {
    id: number;
    product_id: number;
    quantity: number;
    unit_price: number;
    product: Product;
}

interface Props {
    cartItems: CartItem[];
    subtotal: number;
    [key: string]: unknown;
}

export default function CartIndex({ cartItems, subtotal }: Props) {
    const [processingItems, setProcessingItems] = useState<number[]>([]);

    const updateQuantity = (itemId: number, newQuantity: number) => {
        if (newQuantity < 1) return;

        setProcessingItems(prev => [...prev, itemId]);

        router.patch(route('cart.update', itemId), {
            quantity: newQuantity,
        }, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                setProcessingItems(prev => prev.filter(id => id !== itemId));
            },
        });
    };

    const removeItem = (itemId: number) => {
        setProcessingItems(prev => [...prev, itemId]);

        router.delete(route('cart.destroy', itemId), {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                setProcessingItems(prev => prev.filter(id => id !== itemId));
            },
        });
    };

    const formatPrice = (price: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };

    const getTotalPrice = (item: CartItem) => {
        return item.quantity * item.unit_price;
    };

    return (
        <AppShell>
            <Head title="Shopping Cart" />
            
            <div className="py-6">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-gray-900 mb-2">🛒 Shopping Cart</h1>
                        <p className="text-gray-600">
                            {cartItems.length} {cartItems.length === 1 ? 'item' : 'items'} in your cart
                        </p>
                    </div>

                    {cartItems.length === 0 ? (
                        <div className="text-center py-12">
                            <ShoppingCart className="w-16 h-16 text-gray-400 mx-auto mb-4" />
                            <h3 className="text-lg font-semibold text-gray-900 mb-2">Your cart is empty</h3>
                            <p className="text-gray-600 mb-6">
                                Start shopping and add some products to your cart.
                            </p>
                            <Link 
                                href={route('products.index')} 
                                className="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center"
                            >
                                <Package className="w-5 h-5 mr-2" />
                                Browse Products
                            </Link>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            {/* Cart Items */}
                            <div className="lg:col-span-2">
                                <div className="bg-white rounded-xl shadow-md overflow-hidden">
                                    <div className="p-6 border-b border-gray-200">
                                        <h2 className="text-lg font-semibold text-gray-900">Cart Items</h2>
                                    </div>
                                    
                                    <div className="divide-y divide-gray-200">
                                        {cartItems.map((item) => (
                                            <div
                                                key={item.id}
                                                className={`p-6 ${processingItems.includes(item.id) ? 'opacity-50' : ''}`}
                                            >
                                                <div className="flex items-start space-x-4">
                                                    {/* Product Image */}
                                                    <div className="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                                        {item.product.images && item.product.images[0] ? (
                                                            <img
                                                                src={item.product.images[0]}
                                                                alt={item.product.name}
                                                                className="w-full h-full object-cover"
                                                            />
                                                        ) : (
                                                            <div className="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                                <Package className="w-8 h-8 text-gray-400" />
                                                            </div>
                                                        )}
                                                    </div>

                                                    {/* Product Details */}
                                                    <div className="flex-1 min-w-0">
                                                        <Link
                                                            href={route('products.show', item.product.slug)}
                                                            className="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors"
                                                        >
                                                            {item.product.name}
                                                        </Link>
                                                        <p className="text-sm text-gray-500 mb-2">
                                                            {item.product.category.name}
                                                        </p>
                                                        <p className="text-lg font-bold text-gray-900">
                                                            {formatPrice(item.unit_price)}
                                                        </p>
                                                    </div>

                                                    {/* Quantity Controls */}
                                                    <div className="flex items-center space-x-3">
                                                        <div className="flex items-center border border-gray-300 rounded-lg">
                                                            <button
                                                                onClick={() => updateQuantity(item.id, item.quantity - 1)}
                                                                disabled={item.quantity <= 1 || processingItems.includes(item.id)}
                                                                className="p-1 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                            >
                                                                <Minus className="w-4 h-4" />
                                                            </button>
                                                            <span className="px-3 py-1 text-sm font-semibold min-w-[2rem] text-center">
                                                                {item.quantity}
                                                            </span>
                                                            <button
                                                                onClick={() => updateQuantity(item.id, item.quantity + 1)}
                                                                disabled={processingItems.includes(item.id)}
                                                                className="p-1 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                            >
                                                                <Plus className="w-4 h-4" />
                                                            </button>
                                                        </div>

                                                        <button
                                                            onClick={() => removeItem(item.id)}
                                                            disabled={processingItems.includes(item.id)}
                                                            className="p-2 text-red-600 hover:bg-red-50 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                                            title="Remove item"
                                                        >
                                                            <Trash2 className="w-4 h-4" />
                                                        </button>
                                                    </div>

                                                    {/* Item Total */}
                                                    <div className="text-right">
                                                        <p className="text-lg font-bold text-gray-900">
                                                            {formatPrice(getTotalPrice(item))}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>

                            {/* Order Summary */}
                            <div className="lg:col-span-1">
                                <div className="bg-white rounded-xl shadow-md p-6 sticky top-4">
                                    <h2 className="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                                    
                                    <div className="space-y-3 mb-6">
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Subtotal</span>
                                            <span className="font-semibold">{formatPrice(subtotal)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Shipping</span>
                                            <span className="text-sm text-gray-500">Calculated at checkout</span>
                                        </div>
                                        <div className="border-t pt-3">
                                            <div className="flex justify-between">
                                                <span className="text-lg font-semibold">Total</span>
                                                <span className="text-lg font-bold text-blue-600">{formatPrice(subtotal)}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <button
                                        onClick={() => router.visit(route('checkout.index'))}
                                        className="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors font-semibold"
                                    >
                                        Proceed to Checkout
                                    </button>

                                    <Link
                                        href={route('products.index')}
                                        className="w-full mt-3 bg-gray-100 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-200 transition-colors font-semibold text-center block"
                                    >
                                        Continue Shopping
                                    </Link>

                                    {/* Cart Summary */}
                                    <div className="mt-6 p-4 bg-gray-50 rounded-lg">
                                        <h3 className="text-sm font-semibold text-gray-700 mb-2">Quick Stats</h3>
                                        <div className="space-y-2 text-sm">
                                            <div className="flex justify-between">
                                                <span className="text-gray-600">Items</span>
                                                <span>{cartItems.reduce((sum, item) => sum + item.quantity, 0)}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-gray-600">Products</span>
                                                <span>{cartItems.length}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-gray-600">Average Price</span>
                                                <span>{formatPrice(subtotal / cartItems.reduce((sum, item) => sum + item.quantity, 0))}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AppShell>
    );
}