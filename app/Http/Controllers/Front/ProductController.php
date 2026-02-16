<?php


namespace App\Http\Controllers\Front;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Liste des produits (front API)
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Product::with([
            'featuredImage',
            'images',
            'brand',
            'categories',
            'labels',
            'collections',
            'store',
        ]);

        // 🔹 Filtrage
        if ($request->filled('category_id')) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $request->category_id));
        }
        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) => $q->where('categories.slug', $request->category));
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('collection_id')) {
            $query->whereHas('collections', fn($q) => $q->where('product_collections.id', $request->collection_id));
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', 1);
        }

        // 🔹 Recherche par nom
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // 🔹 Pagination
        $perPage = $request->get('per_page', 12);
        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Afficher un produit unique
     * @param $slug
     * @return ProductResource
     */
    public function show($slug)
    {
        $product = Product::with([
            'featuredImage',
            'images',
            'brand',
            'categories',
            'labels',
            'collections',
            'store',
        ])->where(['slug'=>$slug])->firstOrFail();

        logger($product);
        return new ProductResource($product);
    }

    /**
     * Liste des produits par store
     */
    public function byStore($storeId, Request $request)
    {
        $query = Product::with([
            'featuredImage',
            'images',
            'brand',
            'categories',
            'labels',
            'collections',
        ])->where('store_id', $storeId);

        $perPage = $request->get('per_page', 12);
        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }
    public function producttabs(Request $request)
    {
        // ================= FEATURED =================
        $featured = Product::with(['featuredImage', 'brand', 'categories', 'labels', 'collections','store'])
            ->where('is_featured', 1)
            ->take(12)
            ->get();

        // ================= BEST SELLER =================
        $bestSeller = Product::with(['featuredImage', 'brand', 'categories', 'labels', 'collections','store'])
            ->select('products.*')
            ->join('order_product', 'products.id', '=', 'order_product.product_id')
            ->groupBy('products.id')
            ->orderByRaw('SUM(order_product.qty) DESC')
            ->take(12)
            ->get();

        // ================= MOST VIEWED =================
        $mostViewed = Product::with(['featuredImage', 'brand', 'categories', 'labels', 'collections','store'])
            ->orderByDesc('views')
            ->take(12)
            ->get();

        // ================= RESPONSE =================
        return response()->json([
            [
                'title' => 'En vedette',
                'products' => ProductResource::collection($featured)
            ],
            [
                'title' => 'Meilleures ventes',
                'products' => ProductResource::collection($bestSeller)
            ],
            [
                'title' => 'Les plus consultés',
                'products' => ProductResource::collection($mostViewed)
            ]
        ]);
    }

    /**
     * Retourne les produits les plus vendus
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function bestProducts(Request $request)
    {
        $perPage = $request->get('per_page', 12);

        $products = Product::with(['featuredImage', 'brand', 'categories', 'labels', 'collections'])
            ->select('products.*')
            ->join('order_product', 'products.id', '=', 'order_product.product_id')
            ->groupBy('products.id')
            ->orderByRaw('SUM(order_product.qty) DESC')
            ->take($perPage)
            ->get();

        return ProductResource::collection($products);
    }
    /**
     * Produits “Trending” (les plus vus)
     */
    public function trendingProducts(Request $request)
    {
        $perPage = $request->get('per_page', 12);

        $products = Product::with(['featuredImage', 'brand', 'categories', 'labels', 'collections'])
            ->orderByDesc('views') // tri par nombre de vues
            ->take($perPage)
            ->get();

        return ProductResource::collection($products);
    }

    /**
     * Produits “Top Products” basés sur ventes + vues
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function topProducts(Request $request)
    {
        $perPage = $request->get('per_page', 12);

        $products = Product::with(['featuredImage', 'brand', 'categories', 'labels', 'collections'])
            ->select('products.*', DB::raw('COALESCE(SUM(order_product.qty), 0) + products.views as popularity_score'))
            ->leftJoin('order_product', 'products.id', '=', 'order_product.product_id')
            ->groupBy('products.id')
            ->orderByDesc('popularity_score')
            ->take($perPage)
            ->get();

        return ProductResource::collection($products);
    }

    public function productByType(string $type, Request $request)
    {
        $perPage = $request->get('per_page', 16);

        $query = Product::query()
            ->with([
                'featuredImage',
                'images',
                'brand',
                'categories',
                'labels',
                'collections',
                'store',
            ]);

        switch ($type) {

            case 'top-product':
                $query
                    ->leftJoin('order_product', 'products.id', '=', 'order_product.product_id')
                    ->select(
                        'products.*',
                        DB::raw('COALESCE(SUM(order_product.qty), 0) + products.views as popularity_score')
                    )
                    ->groupBy('products.id')
                    ->orderByDesc('popularity_score');
                break;

            case 'best-seller':
                $query
                    ->join('order_product', 'products.id', '=', 'order_product.product_id')
                    ->select(
                        'products.*',
                        DB::raw('SUM(order_product.qty) as total_sold')
                    )
                    ->groupBy('products.id')
                    ->orderByDesc('total_sold');
                break;
            case 'offre-special':
                break;
            case 'offre-flash':
                break;
            default:
                $query->latest(); // produits récents
                break;
        }

        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    public function featuredProducts(Request $request)
    {

        $products = Product::with(['featuredImage'])
       /*     ->select('products.*', DB::raw('COALESCE(SUM(order_product.qty), 0) + products.views as popularity_score'))
            ->leftJoin('order_product', 'products.id', '=', 'order_product.product_id')
            ->groupBy('products.id')
            ->orderByDesc('popularity_score')*/
            ->take(3)
            ->get();

        return ProductResource::collection($products);
    }
}
