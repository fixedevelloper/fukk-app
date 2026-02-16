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
use App\Models\City;
use App\Models\Image;
use App\Models\ProductCollection;
use App\Models\ShippingMethod;
use App\Models\Slider;
use App\Models\Store;
use App\Models\Zone;
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
        $query = Category::with('image', 'children')->withCount('products'); // image + sous-catégories

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
            ->whereIn('id', [1, 6, 15, 19])
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
    public function cities(){
        $cities = City::orderBy('name', 'asc')
            ->select('id', 'name', 'latitude', 'longitude')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }

    public function zones(Request $request){
        $query = Zone::query()
            ->select('id', 'name', 'city_id', 'latitude', 'longitude')
            ->orderBy('name', 'asc');

        // Filtrer par ville si fourni
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        $zones = $query->get();

        return response()->json([
            'success' => true,
            'data' => $zones
        ]);
    }

    public function calculateByZone(Request $request)
    {

        $zone = Zone::findOrFail($request->zone_id);

        $baseLat = config('shipping.base_latitude');
        $baseLng = config('shipping.base_longitude');

        $distance = $this->distance(
            $baseLat,
            $baseLng,
            $zone->latitude,
            $zone->longitude
        );

        $basePrice = 500;
        $pricePerKm = 400;

        $calculatedPrice = $basePrice + ($distance * $pricePerKm);

        return response()->json([
            'success' => true,
            'zone' => $zone->name,
            'distance_km' => round($distance, 2),
            'price' => round($calculatedPrice),
        ]);
    }
    public function getMethods(Request $request)
    {
        $city = City::findOrFail($request->city_id);
        $zone = $request->zone_id ? Zone::find($request->zone_id) : null;

        $methods = ShippingMethod::where('city_id', $city->id)
            ->where('active', true)
            ->get();

        $response = [];

        foreach ($methods as $method) {

            // 🎯 SI GRATUIT → priorité
            if ($method->is_free) {
                $price = 0;
            } else {

                $price = $method->base_price;

                if ($method->type === 'distance') {

                    if (!$zone) continue;

                    $distance = $this->calculateDistance(
                        3.8480,
                        11.5021,
                        $zone->latitude,
                        $zone->longitude
                    );

                    $price = $method->base_price + ($distance * $method->price_per_km);
                }
            }

            $response[] = [
                'id' => $method->id,
                'value' => $method->name,
                'title' => $method->title,
                'description' => $method->description,
                'price' => round($price),
                'is_free' => $method->is_free
            ];
        }

        return response()->json([
            'data' => $response
        ]);
    }

    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) *
            sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

}
