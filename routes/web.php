<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShoppingCartController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

// Home page with e-commerce functionality
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Dashboard (requires authentication)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Shopping cart routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [ShoppingCartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [ShoppingCartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{shoppingCart}', [ShoppingCartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{shoppingCart}', [ShoppingCartController::class, 'destroy'])->name('cart.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';