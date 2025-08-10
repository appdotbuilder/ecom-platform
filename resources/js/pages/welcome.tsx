import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { ShoppingCart, Users, TrendingUp, Zap, Package, CreditCard, Truck, Star } from 'lucide-react';

interface Category {
    id: number;
    name: string;
    slug: string;
    image?: string;
}

interface Product {
    id: number;
    name: string;
    slug: string;
    short_description: string;
    base_price: number;
    user_price: number;
    images: string[];
    category: Category;
    is_featured: boolean;
}

interface UserStats {
    user_type: string;
    reseller_level?: string;
    total_sales: number;
    total_commission: number;
    affiliate_code?: string;
    downlines_count: number;
}

interface Props {
    auth: {
        user?: {
            id: number;
            name: string;
            email: string;
            user_type: string;
        };
    };
    featuredProducts: Product[];
    categories: Category[];
    latestProducts: Product[];
    userStats?: UserStats;
    [key: string]: unknown;
}

export default function Welcome({ auth, featuredProducts, categories, latestProducts, userStats }: Props) {
    const handleAddToCart = (productId: number) => {
        if (!auth.user) {
            router.visit(route('login'));
            return;
        }

        router.post(route('cart.store'), {
            product_id: productId,
            quantity: 1,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const formatPrice = (price: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };

    return (
        <>
            <Head title="🛒 Advanced E-Commerce Platform" />
            <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
                {/* Header */}
                <header className="bg-white shadow-sm sticky top-0 z-50">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between items-center py-4">
                            <div className="flex items-center space-x-4">
                                <div className="flex items-center space-x-2">
                                    <Package className="w-8 h-8 text-blue-600" />
                                    <h1 className="text-2xl font-bold text-gray-900">ECommerce Pro</h1>
                                </div>
                            </div>
                            
                            <nav className="flex items-center space-x-4">
                                <Link href={route('products.index')} className="text-gray-700 hover:text-blue-600 font-medium">
                                    Products
                                </Link>
                                {auth.user ? (
                                    <>
                                        <Link href={route('cart.index')} className="flex items-center space-x-1 text-gray-700 hover:text-blue-600">
                                            <ShoppingCart className="w-5 h-5" />
                                            <span>Cart</span>
                                        </Link>
                                        <Link href={route('dashboard')} className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                            Dashboard
                                        </Link>
                                    </>
                                ) : (
                                    <>
                                        <Link href={route('login')} className="text-gray-700 hover:text-blue-600 font-medium">
                                            Login
                                        </Link>
                                        <Link href={route('register')} className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                            Register
                                        </Link>
                                    </>
                                )}
                            </nav>
                        </div>
                    </div>
                </header>

                {/* Hero Section */}
                <section className="py-20 px-4 sm:px-6 lg:px-8">
                    <div className="max-w-7xl mx-auto text-center">
                        <h2 className="text-5xl font-extrabold text-gray-900 mb-6">
                            🚀 Advanced B2B & B2C E-Commerce Platform
                        </h2>
                        <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                            Complete solution with multi-level reseller program, affiliate system, POS integration, 
                            and advanced payment options including Midtrans, Xendit, and "pay later" system.
                        </p>
                        
                        {userStats && (
                            <div className="bg-white rounded-xl shadow-lg p-6 max-w-4xl mx-auto mb-8">
                                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div className="text-center">
                                        <div className="text-2xl font-bold text-blue-600">{userStats.user_type.toUpperCase()}</div>
                                        <div className="text-gray-500">Account Type</div>
                                        {userStats.reseller_level && (
                                            <div className="text-sm text-purple-600 font-medium">{userStats.reseller_level} Level</div>
                                        )}
                                    </div>
                                    <div className="text-center">
                                        <div className="text-2xl font-bold text-green-600">{formatPrice(userStats.total_sales)}</div>
                                        <div className="text-gray-500">Total Sales</div>
                                    </div>
                                    <div className="text-center">
                                        <div className="text-2xl font-bold text-orange-600">{formatPrice(userStats.total_commission)}</div>
                                        <div className="text-gray-500">Commission Earned</div>
                                    </div>
                                    <div className="text-center">
                                        <div className="text-2xl font-bold text-purple-600">{userStats.downlines_count}</div>
                                        <div className="text-gray-500">Downlines</div>
                                        {userStats.affiliate_code && (
                                            <div className="text-sm text-blue-600 font-mono">{userStats.affiliate_code}</div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        )}

                        <div className="flex flex-wrap justify-center gap-4">
                            {!auth.user && (
                                <Link href={route('register')} className="bg-blue-600 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-blue-700 transition-all transform hover:scale-105 shadow-lg">
                                    🎯 Start Selling Today
                                </Link>
                            )}
                            <Link href={route('products.index')} className="bg-white text-blue-600 border-2 border-blue-600 px-8 py-4 rounded-xl text-lg font-semibold hover:bg-blue-50 transition-all transform hover:scale-105 shadow-lg">
                                🛍️ Browse Products
                            </Link>
                        </div>
                    </div>
                </section>

                {/* Key Features */}
                <section className="py-16 bg-white">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h3 className="text-3xl font-bold text-center text-gray-900 mb-12">🌟 Platform Features</h3>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                            <div className="text-center p-6 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 hover:shadow-lg transition-shadow">
                                <Users className="w-12 h-12 text-blue-600 mx-auto mb-4" />
                                <h4 className="text-lg font-bold text-gray-900 mb-2">Multi-Level Reseller</h4>
                                <p className="text-gray-600">10-level reseller program with automatic commission distribution</p>
                            </div>
                            
                            <div className="text-center p-6 rounded-xl bg-gradient-to-br from-green-50 to-green-100 hover:shadow-lg transition-shadow">
                                <TrendingUp className="w-12 h-12 text-green-600 mx-auto mb-4" />
                                <h4 className="text-lg font-bold text-gray-900 mb-2">Affiliate System</h4>
                                <p className="text-gray-600">Advanced affiliate marketing with performance tracking</p>
                            </div>
                            
                            <div className="text-center p-6 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 hover:shadow-lg transition-shadow">
                                <Zap className="w-12 h-12 text-purple-600 mx-auto mb-4" />
                                <h4 className="text-lg font-bold text-gray-900 mb-2">POS Integration</h4>
                                <p className="text-gray-600">Complete POS system with barcode scanning & inventory sync</p>
                            </div>
                            
                            <div className="text-center p-6 rounded-xl bg-gradient-to-br from-orange-50 to-orange-100 hover:shadow-lg transition-shadow">
                                <CreditCard className="w-12 h-12 text-orange-600 mx-auto mb-4" />
                                <h4 className="text-lg font-bold text-gray-900 mb-2">Payment Options</h4>
                                <p className="text-gray-600">Midtrans, Xendit, bank transfer, and pay later system</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                            <div className="text-center p-6 rounded-xl bg-gradient-to-br from-red-50 to-red-100 hover:shadow-lg transition-shadow">
                                <Truck className="w-12 h-12 text-red-600 mx-auto mb-4" />
                                <h4 className="text-lg font-bold text-gray-900 mb-2">Smart Shipping</h4>
                                <p className="text-gray-600">Dynamic shipping costs via Raja Ongkir API integration</p>
                            </div>
                            
                            <div className="text-center p-6 rounded-xl bg-gradient-to-br from-yellow-50 to-yellow-100 hover:shadow-lg transition-shadow">
                                <Package className="w-12 h-12 text-yellow-600 mx-auto mb-4" />
                                <h4 className="text-lg font-bold text-gray-900 mb-2">Inventory Management</h4>
                                <p className="text-gray-600">Real-time stock tracking with automatic reorder alerts</p>
                            </div>
                            
                            <div className="text-center p-6 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 hover:shadow-lg transition-shadow">
                                <Star className="w-12 h-12 text-indigo-600 mx-auto mb-4" />
                                <h4 className="text-lg font-bold text-gray-900 mb-2">Level Progression</h4>
                                <p className="text-gray-600">Automatic level upgrades based on sales performance</p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Categories */}
                <section className="py-16 bg-gray-50">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h3 className="text-3xl font-bold text-center text-gray-900 mb-12">🏪 Product Categories</h3>
                        
                        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                            {categories.map((category) => (
                                <Link
                                    key={category.id}
                                    href={route('products.index', { category: category.id })}
                                    className="group bg-white rounded-xl p-6 text-center hover:shadow-lg transition-all transform hover:scale-105"
                                >
                                    <div className="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:from-blue-200 group-hover:to-blue-300 transition-all">
                                        <Package className="w-8 h-8 text-blue-600" />
                                    </div>
                                    <h4 className="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {category.name}
                                    </h4>
                                </Link>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Featured Products */}
                <section className="py-16 bg-white">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex items-center justify-between mb-12">
                            <h3 className="text-3xl font-bold text-gray-900">⭐ Featured Products</h3>
                            <Link href={route('products.index')} className="text-blue-600 hover:text-blue-700 font-medium">
                                View All →
                            </Link>
                        </div>
                        
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            {featuredProducts.slice(0, 8).map((product) => (
                                <div key={product.id} className="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                    <div className="aspect-w-16 aspect-h-9 bg-gray-100">
                                        {product.images && product.images[0] ? (
                                            <img
                                                src={product.images[0]}
                                                alt={product.name}
                                                className="w-full h-48 object-cover"
                                            />
                                        ) : (
                                            <div className="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                <Package className="w-16 h-16 text-gray-400" />
                                            </div>
                                        )}
                                    </div>
                                    
                                    <div className="p-4">
                                        <div className="flex items-start justify-between mb-2">
                                            <h4 className="font-semibold text-gray-900 text-sm line-clamp-2 flex-1">
                                                {product.name}
                                            </h4>
                                            {product.is_featured && (
                                                <Star className="w-4 h-4 text-yellow-500 flex-shrink-0 ml-2" />
                                            )}
                                        </div>
                                        
                                        <p className="text-gray-600 text-xs mb-3 line-clamp-2">
                                            {product.short_description}
                                        </p>
                                        
                                        <div className="flex items-center justify-between">
                                            <div>
                                                {auth.user && product.user_price !== product.base_price ? (
                                                    <div>
                                                        <div className="text-lg font-bold text-green-600">
                                                            {formatPrice(product.user_price)}
                                                        </div>
                                                        <div className="text-sm text-gray-400 line-through">
                                                            {formatPrice(product.base_price)}
                                                        </div>
                                                    </div>
                                                ) : (
                                                    <div className="text-lg font-bold text-gray-900">
                                                        {formatPrice(product.base_price)}
                                                    </div>
                                                )}
                                            </div>
                                            
                                            <button
                                                onClick={() => handleAddToCart(product.id)}
                                                className="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors"
                                                title="Add to Cart"
                                            >
                                                <ShoppingCart className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Latest Products */}
                <section className="py-16 bg-gray-50">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h3 className="text-3xl font-bold text-center text-gray-900 mb-12">🆕 Latest Products</h3>
                        
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            {latestProducts.map((product) => (
                                <div key={product.id} className="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                    <div className="aspect-w-16 aspect-h-9 bg-gray-100">
                                        {product.images && product.images[0] ? (
                                            <img
                                                src={product.images[0]}
                                                alt={product.name}
                                                className="w-full h-48 object-cover"
                                            />
                                        ) : (
                                            <div className="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                <Package className="w-16 h-16 text-gray-400" />
                                            </div>
                                        )}
                                    </div>
                                    
                                    <div className="p-4">
                                        <h4 className="font-semibold text-gray-900 text-sm mb-2 line-clamp-2">
                                            {product.name}
                                        </h4>
                                        
                                        <p className="text-gray-600 text-xs mb-3 line-clamp-2">
                                            {product.short_description}
                                        </p>
                                        
                                        <div className="flex items-center justify-between">
                                            <div>
                                                {auth.user && product.user_price !== product.base_price ? (
                                                    <div>
                                                        <div className="text-lg font-bold text-green-600">
                                                            {formatPrice(product.user_price)}
                                                        </div>
                                                        <div className="text-sm text-gray-400 line-through">
                                                            {formatPrice(product.base_price)}
                                                        </div>
                                                    </div>
                                                ) : (
                                                    <div className="text-lg font-bold text-gray-900">
                                                        {formatPrice(product.base_price)}
                                                    </div>
                                                )}
                                            </div>
                                            
                                            <button
                                                onClick={() => handleAddToCart(product.id)}
                                                className="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors"
                                                title="Add to Cart"
                                            >
                                                <ShoppingCart className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Footer */}
                <footer className="bg-gray-900 text-white py-12">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center">
                            <div className="flex items-center justify-center space-x-2 mb-4">
                                <Package className="w-8 h-8 text-blue-400" />
                                <h4 className="text-2xl font-bold">ECommerce Pro</h4>
                            </div>
                            <p className="text-gray-400 mb-6">
                                Advanced B2B & B2C E-Commerce Platform with Multi-Level Marketing
                            </p>
                            <p className="text-sm text-gray-500">
                                Built with ❤️ by{' '}
                                <a href="https://app.build" target="_blank" className="text-blue-400 hover:text-blue-300">
                                    app.build
                                </a>
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}