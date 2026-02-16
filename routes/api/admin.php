<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ShippingMethodController;
use App\Http\Controllers\Admin\ZoneController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminHookController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\VendorOrderController;
use App\Http\Controllers\Admin\VendorDashboardController;
use App\Http\Controllers\Admin\StoreController;

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (PROTECTED)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    // ->prefix('admin')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        Route::get('/vendors/dashboard', [VendorDashboardController::class, 'index']);

        /*
        |--------------------------------------------------------------------------
        | Sliders
        |--------------------------------------------------------------------------
        */
        Route::apiResource('sliders', SliderController::class);
        Route::patch('/sliders/{slider}/toggle', [SliderController::class, 'toggle']);

        /*
        |--------------------------------------------------------------------------
        | Banners
        |--------------------------------------------------------------------------
        */
        Route::apiResource('banners', BannerController::class);
        Route::patch('banners/{banner}/toggle-active', [BannerController::class, 'toggleActive']);

        /*
        |--------------------------------------------------------------------------
        | Brands
        |--------------------------------------------------------------------------
        */
        Route::apiResource('brands', BrandController::class);

        /*
        |--------------------------------------------------------------------------
        | Stores
        |--------------------------------------------------------------------------
        */
        Route::apiResource('admin-stores', StoreController::class);

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */
       // Route::post('/products', [ProductController::class, 'store']);
        Route::apiResource('admin-products', ProductController::class);
        /*
        |--------------------------------------------------------------------------
        | Vendor Orders
        |--------------------------------------------------------------------------
        */
        Route::get('/vendors/orders', [VendorOrderController::class, 'index']);
        Route::get('/vendors/orders/{id}', [VendorOrderController::class, 'show']);
        Route::put('/vendors/orders/{id}/status', [VendorOrderController::class, 'updateStatus']);

        /*
        |--------------------------------------------------------------------------
        | Attributes
        |--------------------------------------------------------------------------
        */
        Route::get('/attributes', [AdminHookController::class, 'getAttributes']);
        Route::post('/attributes', [AdminHookController::class, 'storeAttribut']);

        Route::get('/attribute-sets', [AdminHookController::class, 'getAttributSet']);
        Route::post('/attribute-sets', [AdminHookController::class, 'storeAttributSet']);

        /*
         |--------------------------------------------------------------------------
         | Categories
         |--------------------------------------------------------------------------
        */
        Route::apiResource('admin-categories', CategoryController::class);
        Route::get('admin-categories-parents', [CategoryController::class, 'categorieParent']);
       /*
       |--------------------------------------------------------------------------
       | Images
       |--------------------------------------------------------------------------
      */
        Route::post('/images', [AdminHookController::class, 'storeImage']);

        Route::apiResource('shipping-methods', ShippingMethodController::class);

        Route::apiResource('zones', ZoneController::class);
        Route::apiResource('cities', CityController::class);
        Route::get('cities/zones/{id}', [CityController::class, 'zoneByCityId']);
        Route::get('shipping-methods/byCity/{id}', [ShippingMethodController::class, 'shippinMethodsByCityId']);

    });
