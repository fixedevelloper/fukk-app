<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Front\HookController;
use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\OrderController;
use App\Http\Controllers\Front\StoreController;
use App\Http\Controllers\Admin\VendorDashboardController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/vendor/register-with-store', [VendorDashboardController::class, 'registerWithStore']);

/*
|--------------------------------------------------------------------------
| PRODUCTS
|--------------------------------------------------------------------------
*/
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{slug}', [ProductController::class, 'show']);
    Route::get('/store/{storeId}', [ProductController::class, 'byStore']);
});

Route::get('/productbytype/{type}', [ProductController::class, 'productByType']);
Route::get('/product-featured', [ProductController::class, 'featuredProducts']);
Route::get('/best-products', [ProductController::class, 'bestProducts']);
Route::get('/trending-products', [ProductController::class, 'trendingProducts']);
Route::get('/top-products', [ProductController::class, 'topProducts']);
Route::get('/tabs', [ProductController::class, 'producttabs']);

/*
|--------------------------------------------------------------------------
| STORES
|--------------------------------------------------------------------------
*/
Route::get('/stores', [StoreController::class, 'stores']);
Route::get('/stores/{store}', [StoreController::class, 'storeById']);
Route::get('/stores/products/{store}', [StoreController::class, 'products']);

/*
|--------------------------------------------------------------------------
| ORDERS
|--------------------------------------------------------------------------
*/
Route::prefix('orders')->group(function () {
    Route::post('/place-order', [OrderController::class, 'placeOrder'])
        ->middleware('auth:sanctum');

    Route::get('/my-orders', [OrderController::class, 'myOrders'])
        ->middleware('auth:sanctum');

    Route::get('/store-sale/{store}', [OrderController::class, 'storeSales']);
});

/*
|--------------------------------------------------------------------------
| CONTENT / HOOKS
|--------------------------------------------------------------------------
*/
Route::get('/app-sliders', [HookController::class, 'index']);
Route::get('/app-banners', [HookController::class, 'banners']);
Route::get('/app-brands', [HookController::class, 'brands']);
Route::get('/mega-menu-categories', [HookController::class, 'categorieMegaMenu']);
Route::get('/categories', [HookController::class, 'categories']);
Route::get('/categories/menus', [HookController::class, 'categoriesMenu']);
Route::get('/collections', [HookController::class, 'productCollections']);
Route::get('/images', [HookController::class, 'images']);
