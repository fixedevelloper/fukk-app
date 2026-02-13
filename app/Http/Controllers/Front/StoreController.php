<?php

namespace App\Http\Controllers\Front;

use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreResource;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class StoreController extends Controller
{
    /**
     *
     * @param Request $request
     * @param $storeId
     */
    public function storeById(Request $request, $storeId)
    {
        $store = Store::with([
            'vendor',
        ])->findOrFail($storeId);

        return new StoreResource($store);
    }

    /**
     * Liste des produits d'une boutique (paginated)
     * @param Request $request
     * @param $storeId
     * @return AnonymousResourceCollection
     */
    public function products(Request $request, $storeId)
    {
        $perPage = $request->get('per_page', 16);

        $store = Store::findOrFail($storeId);

        $products = Product::
        with([
            'featuredImage',
            'images',
            'brand',
            'categories',
            'labels',
            'collections',
            'store',
        ])->where('store_id', $store->id)
            //->where('status', 'active')
            ->latest()
            ->paginate($perPage);

        return ProductResource::collection($products);
    }
    /**
     * Liste des boutiques d'une boutique (paginated)
     * @param Request $request
     * @param $storeId
     * @return AnonymousResourceCollection
     */
    public function stores(Request $request)
    {
        $perPage = $request->get('per_page', 16);


        $stores = Store::where('status', 'active')
            ->latest()
            ->paginate($perPage);

        return StoreResource::collection($stores);
    }
}

