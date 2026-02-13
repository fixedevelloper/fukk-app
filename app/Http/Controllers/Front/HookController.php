<?php


namespace App\Http\Controllers\Front;


use App\Http\Resources\BannerResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\BrandSelectResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\ProductCollectionResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\StoreResource;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\ProductCollection;
use App\Models\Slider;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class HookController extends Controller
{

    /**
     * Retourne la liste des marques
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function brands(Request $request)
    {
        $query = Brand::with('image'); // charge l'image liée

        // 🔹 Option : filtrer par marque "active/published"
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔹 Pagination facultative
        $perPage = $request->get('per_page', 20);
        $brands = $query->paginate($perPage);

        return BrandResource::collection($brands);
    }
    public function categories(Request $request)
    {
        $query = Category::with('image', 'children','products'); // image + sous-catégories

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 10);
        $categories = $query->paginate($perPage);

        return CategoryResource::collection($categories);
    }

    public function categorieMegaMenu(Request $request)
    {
        $categories = Category::with([
            'image',
            'children.image',
            'children.children.image'
        ])
            ->where('parent_id','=',0)
            ->orderBy('order')
            ->get();

        return CategoryResource::collection($categories);
    }



    public function categoriesMenu(Request $request)
    {
        $menuIds = config('menu.categories');
        $categories = Category::query()
            ->with([
                'image',
                'children'
            ])
            ->whereIn('id', [3, 6, 15, 19])
      /*      ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })*/
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function stores(Request $request)
    {
        $query = Store::with(['logo', 'cover_image', 'city']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 20);
        $stores = $query->paginate($perPage);

        return StoreResource::collection($stores);
    }
    public function productCollections(Request $request)
    {
        $query = ProductCollection::with('image', 'products');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 20);
        $collections = $query->paginate($perPage);

        return ProductCollectionResource::collection($collections);
    }
    public function images(Request $request)
    {
        $perPage = $request->integer('per_page', 20);

        $images = Image::latest()->paginate($perPage);

        return ImageResource::collection($images);
    }

    public function storeImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:5120', // max 5MB
            'name' => 'required|string|max:255',
            'alt'  => 'nullable|string|max:255',
        ]);

        $image = Image::create([
            'name' => $request->name,
            'alt'  => $request->alt,
            'src'  => $request->alt,
        ]);

        $image->addMediaFromRequest('file')->toMediaCollection('default');

        return response()->json(['data' => new ImageResource($image)], 201);
    }
    public function index(Request $request)
    {
        $sliders = Slider::with('image')
            ->where('is_active', true)
            ->orderBy('position')
            ->get();

        return SliderResource::collection($sliders);
    }
    public function banners(Request $request)
    {
        $query = Banner::with('image');

        if ($request->filled('placement')) {
            $query->where('placement', $request->placement);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return BannerResource::collection(
            $query->latest()->get()
        );
    }

}
