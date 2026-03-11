<?php


namespace App\Http\Controllers\Admin;


use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationItem;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    /**
     * Liste des produits (front API)
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        logger('cccc');
        $user = $request->user();
        $store = Store::where('vendor_id', $user->id)->firstOrFail();
        if (!$store) {
            return response()->json([
                'message' => 'Vous n’avez pas de boutique associée.'
            ], 404);
        }
        $query = Product::with([
            'featuredImage',
            'images',
            'brand',
            'categories',
            'labels',
            'collections',
            'store',
        ])->where('store_id', $store->id);

        // 🔹 Filtrage
        if ($request->filled('category_id')) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $request->category_id));
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
        $perPage = $request->get('limit', 12);
        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Créer un nouveau produit
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user=Auth::user();
        // 1️⃣ Validation
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'image_id' => 'nullable|exists:images,id',
            'attributes' => 'nullable|array',
            'attributes.*' => 'exists:product_attributes,id',
           // 'store_id' => 'required|exists:stores,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $store=Store::query()->where(['vendor_id'=>$user->id])->first();

        if (is_null($store)){
            return response()->json([
                'success' => false,
                'errors' => 'pas de boutique',
            ], 422);
        }
        // 2️⃣ Création du produit
        $data = $request->only([
            'type',
            'name',
            'slug',
            'short_description',
            'description',
            'sku',
            'reference',
            'price',
            'sale_price',
            'quantity',
            'length',
            'wide',
            'height',
            'weight',
            'tax_id',
            'status',
            //'stock_status',
            'allow_checkout_when_out_of_stock',
            'with_storehouse_management',
            'is_featured',
            'image_id',
            'store_id',
        ]);

        // Générer le slug si vide
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        if (empty($data['type'])) {
            $data['is_variation'] = $data['type']=='simple'?false:true;
        }
        DB::beginTransaction();

        $data['store_id']=$store->id;
        $product = Product::create($data);

        // 3️⃣ Associer les catégories
        $product->categories()->sync(array_filter([$request->category_id, $request->sub_category_id]));

        // 4️⃣ Associer les attributs

        if ($request->filled('attributes')) {
            // ici on envoie uniquement les ids numériques
            $attributeIds = array_map(fn($a) => (int)$a, (array)$request->attributes);
            $product->productAttributeSets()->sync($attributeIds);
        }
        if ($request->filled('gallery')) {
            $product->images()->sync($request->gallery ?? []);
        }
        // 🔥 Sauvegarde produits associés
        if ($request->has('related_products')) {
            $product->relatedProducts()->sync($request->related_products);
        }

        $product->variations()->delete();

        foreach ($request->variations as $variationData) {
            $variation = ProductVariation::create([
                'configurable_product_id' => $product->id,
                'price' => $variationData['price'],
                'stock' => $variationData['stock'],
                'sku' => $variationData['sku'],
                'is_default' => $variationData['is_default']
            ]);

            foreach ($variationData['attributes'] as $attributeId) {
                ProductVariationItem::create([
                    'variation_id' => $variation->id,
                    'attribute_id' => $attributeId
                ]);
            }
        }



        DB::commit();
        // 5️⃣ Retour
        return response()->json([
            'success' => true,
            'data' => $product->load('categories', 'productAttributeSets', 'store', 'featuredImage'),
        ]);
    }
    public function show($id)
    {
        $product = Product::with([
            'featuredImage',
            'images',
            'brand',
            'categories',
            'labels',
            'collections',
            'store', 'store.logo',
            'variations.attributeValues.attribute'
        ])->findOrFail($id);

        return new ProductResource($product);
    }
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $product = Product::findOrFail($id);

        // 1️⃣ Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'image_id' => 'nullable|exists:images,id',
            'attributes' => 'nullable|array',
            'attributes.*' => 'exists:product_attributes,id',
            'gallery' => 'nullable|array',
            'gallery.*' => 'exists:images,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $store = Store::query()->where(['vendor_id' => $user->id])->first();
        if (is_null($store)) {
            return response()->json([
                'success' => false,
                'errors' => 'Pas de boutique',
            ], 422);
        }

        // 2️⃣ Mettre à jour les champs du produit
        $data = $request->only([
            'name',
            'type',
            'slug',
            'short_description',
            'description',
            'sku',
            'reference',
            'price',
            'sale_price',
            'quantity',
            'length',
            'wide',
            'height',
            'weight',
            'tax_id',
            'status',
            'allow_checkout_when_out_of_stock',
            'with_storehouse_management',
            'is_featured',
            'image_id',
        ]);

        DB::beginTransaction();
        // Générer le slug si vide
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['store_id'] = $store->id;
        if (empty($data['type'])) {
            $data['is_variation'] = $data['type']=='simple'?false:true;
        }
        $product->update($data);

        // 3️⃣ Synchroniser les catégories
        $product->categories()->sync(array_filter([$request->category_id, $request->sub_category_id]));

        // 4️⃣ Synchroniser les attributs
        if ($request->filled('attributes')) {
            $attributeIds = array_map(fn($a) => (int)$a, (array)$request->attributes);
            $product->productAttributeSets()->sync($attributeIds);
        } else {
            $product->productAttributeSets()->sync([]); // vider si aucun
        }

        // 5️⃣ Synchroniser les images
        if ($request->filled('gallery')) {
            $product->images()->sync($request->gallery);
        } else {
            $product->images()->sync([]); // vider si aucun
        }
        if ($request->has('related_products')) {
            $product->relatedProducts()->sync($request->related_products);
        }
        $product->variations()->delete();

        foreach ($request->variations as $variationData) {
            $variation = ProductVariation::create([
                'configurable_product_id' => $product->id,
                'price' => $variationData['price'],
                'stock' => $variationData['stock'],
                'sku' => $variationData['sku'],
                'is_default' => $variationData['is_default']
            ]);

            foreach ($variationData['attributes'] as $attributeId) {
                ProductVariationItem::create([
                    'variation_id' => $variation->id,
                    'attribute_id' => $attributeId
                ]);
            }
        }

        DB::commit();
        // 6️⃣ Retour
        return response()->json([
            'success' => true,
            'data' => $product->load('categories', 'productAttributeSets', 'store', 'featuredImage'),
        ]);
    }

}

