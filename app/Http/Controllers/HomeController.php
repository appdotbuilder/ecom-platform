<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display the home page with featured products and categories.
     */
    public function index()
    {
        // Get featured products
        $featuredProducts = Product::with('category')
            ->active()
            ->featured()
            ->inStock()
            ->limit(8)
            ->get()
            ->map(function($product) {
                // Calculate user-specific price if authenticated
                $userPrice = $product->base_price;
                if (auth()->check() && auth()->user()->resellerLevel) {
                    $userPrice = $product->getPriceForResellerLevel(auth()->user()->resellerLevel);
                }
                
                return array_merge($product->toArray(), [
                    'user_price' => $userPrice,
                ]);
            });

        // Get main categories
        $categories = Category::active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        // Get latest products
        $latestProducts = Product::with('category')
            ->active()
            ->inStock()
            ->latest()
            ->limit(4)
            ->get()
            ->map(function($product) {
                $userPrice = $product->base_price;
                if (auth()->check() && auth()->user()->resellerLevel) {
                    $userPrice = $product->getPriceForResellerLevel(auth()->user()->resellerLevel);
                }
                
                return array_merge($product->toArray(), [
                    'user_price' => $userPrice,
                ]);
            });

        // Get user-specific data if authenticated
        $userStats = null;
        if (auth()->check()) {
            $user = auth()->user();
            $userStats = [
                'user_type' => $user->user_type,
                'reseller_level' => $user->resellerLevel?->name,
                'total_sales' => $user->total_sales,
                'total_commission' => $user->total_commission_earned,
                'affiliate_code' => $user->affiliate_code,
                'downlines_count' => $user->downlines()->count(),
            ];
        }

        return Inertia::render('welcome', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'latestProducts' => $latestProducts,
            'userStats' => $userStats,
        ]);
    }
}