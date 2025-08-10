import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { ShoppingCart, Search, Filter, Package, Star } from 'lucide-react';

interface Category {
    id: number;
    name: string;
    slug: string;
}

interface Product {
    id: number;
    name: string;
    slug: string;
    short_description: string;
    base_price: number;
    user_price?: number;
    images: string[];
    category: Category;
    is_featured: boolean;
    stock_quantity: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationMeta {
    current_page: number;
    from: number | null;
    to: number | null;
    total: number;
    last_page: number;
}

interface Props {
    products: {
        data: Product[];
        links: PaginationLink[];
        meta: PaginationMeta;
    };
    categories: Category[];
    filters: {
        search?: string;
        category?: number;
    };
    [key: string]: unknown;
}

export default function ProductsIndex({ products, categories, filters }: Props) {
    const handleSearch = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        const formData = new FormData(e.currentTarget);
        const search = formData.get('search') as string;
        
        router.get(route('products.index'), {
            search,
            category: filters.category,
        }, {
            preserveState: true,
        });
    };

    const handleCategoryFilter = (categoryId: number | null) => {
        router.get(route('products.index'), {
            search: filters.search,
            category: categoryId,
        }, {
            preserveState: true,
        });
    };

    const handleAddToCart = (productId: number) => {
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
        <AppShell>
            <Head title="Products" />
            
            <div className="py-6">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-gray-900 mb-4">🛍️ Products</h1>
                        
                        {/* Search and Filters */}
                        <div className="flex flex-col lg:flex-row gap-4">
                            {/* Search */}
                            <form onSubmit={handleSearch} className="flex-1">
                                <div className="relative">
                                    <Search className="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
                                    <input
                                        type="text"
                                        name="search"
                                        defaultValue={filters.search || ''}
                                        placeholder="Search products by name, SKU, or barcode..."
                                        className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                    <button
                                        type="submit"
                                        className="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white px-4 py-1 rounded-md text-sm hover:bg-blue-700 transition-colors"
                                    >
                                        Search
                                    </button>
                                </div>
                            </form>

                            {/* Category Filter */}
                            <div className="flex items-center space-x-2">
                                <Filter className="w-5 h-5 text-gray-400" />
                                <select
                                    value={filters.category || ''}
                                    onChange={(e) => handleCategoryFilter(e.target.value ? parseInt(e.target.value) : null)}
                                    className="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">All Categories</option>
                                    {categories.map((category) => (
                                        <option key={category.id} value={category.id}>
                                            {category.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Results Info */}
                    <div className="mb-6">
                        <p className="text-gray-600">
                            Showing {products.meta.from || 0} - {products.meta.to || 0} of {products.meta.total} products
                        </p>
                    </div>

                    {/* Products Grid */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                        {products.data.map((product) => (
                            <div key={product.id} className="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                <div className="relative">
                                    <Link href={route('products.show', product.slug)}>
                                        {product.images && product.images[0] ? (
                                            <img
                                                src={product.images[0]}
                                                alt={product.name}
                                                className="w-full h-48 object-cover hover:scale-105 transition-transform duration-300"
                                            />
                                        ) : (
                                            <div className="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                <Package className="w-16 h-16 text-gray-400" />
                                            </div>
                                        )}
                                    </Link>
                                    
                                    {product.is_featured && (
                                        <div className="absolute top-2 left-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold flex items-center">
                                            <Star className="w-3 h-3 mr-1" />
                                            Featured
                                        </div>
                                    )}

                                    {product.stock_quantity <= 5 && product.stock_quantity > 0 && (
                                        <div className="absolute top-2 right-2 bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                            Low Stock
                                        </div>
                                    )}

                                    {product.stock_quantity === 0 && (
                                        <div className="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                            <span className="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                                Out of Stock
                                            </span>
                                        </div>
                                    )}
                                </div>
                                
                                <div className="p-4">
                                    <Link href={route('products.show', product.slug)}>
                                        <h3 className="font-semibold text-gray-900 text-sm mb-2 line-clamp-2 hover:text-blue-600 transition-colors">
                                            {product.name}
                                        </h3>
                                    </Link>
                                    
                                    <p className="text-gray-600 text-xs mb-2">
                                        {product.category.name}
                                    </p>
                                    
                                    <p className="text-gray-600 text-xs mb-3 line-clamp-2">
                                        {product.short_description}
                                    </p>
                                    
                                    <div className="flex items-center justify-between">
                                        <div>
                                            {product.user_price && product.user_price !== product.base_price ? (
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
                                        
                                        {product.stock_quantity > 0 && (
                                            <button
                                                onClick={() => handleAddToCart(product.id)}
                                                className="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors"
                                                title="Add to Cart"
                                            >
                                                <ShoppingCart className="w-4 h-4" />
                                            </button>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* No Results */}
                    {products.data.length === 0 && (
                        <div className="text-center py-12">
                            <Package className="w-16 h-16 text-gray-400 mx-auto mb-4" />
                            <h3 className="text-lg font-semibold text-gray-900 mb-2">No products found</h3>
                            <p className="text-gray-600">
                                Try adjusting your search criteria or browse different categories.
                            </p>
                        </div>
                    )}

                    {/* Pagination */}
                    {products.meta.last_page > 1 && (
                        <div className="flex justify-center">
                            <nav className="flex items-center space-x-2">
                                {products.links.map((link, index) => (
                                    <Link
                                        key={index}
                                        href={link.url || '#'}
                                        className={`px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                                            link.active
                                                ? 'bg-blue-600 text-white'
                                                : link.url
                                                ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                        }`}
                                        preserveState
                                    >
                                        <span dangerouslySetInnerHTML={{ __html: link.label }} />
                                    </Link>
                                ))}
                            </nav>
                        </div>
                    )}
                </div>
            </div>
        </AppShell>
    );
}