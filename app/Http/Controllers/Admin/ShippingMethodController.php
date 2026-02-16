<?php


namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Zone;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class ShippingMethodController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN CRUD
    |--------------------------------------------------------------------------
    */

    // Liste
    public function index()
    {
        return response()->json([
            'data' => ShippingMethod::with('city')->latest()->get()
        ]);
    }

    // Création
    public function store(Request $request)
    {
      $validated=  $request->validate([
            'city_id' => 'required|exists:cities,id',
            'title' => 'required|string',
            'type' => 'required|in:pickup,distance',
            'base_price' => 'nullable|numeric',
            'price_per_km' => 'nullable|numeric',
        ]);
        $validated['name']=Str::slug($validated['title']);

        $method = ShippingMethod::create($validated);

        return response()->json([
            'message' => 'Méthode créée avec succès',
            'data' => $method
        ]);
    }

    // Afficher une méthode
    public function show($id)
    {
        return response()->json([
            'data' => ShippingMethod::with('city')->findOrFail($id)
        ]);
    }

    // Mise à jour
    public function update(Request $request, $id)
    {
        $method = ShippingMethod::findOrFail($id);
        $validated=$request->all();
        $validated['name']=Str::slug($validated['title']);
        $method->update($validated);

        return response()->json([
            'message' => 'Méthode mise à jour',
            'data' => $method
        ]);
    }
    /**
     * Récupérer les zones
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function shippinMethodsByCityId($id)
    {
        $methods = ShippingMethod::where('city_id', $id)
            ->where('active', true)
            ->orderBy('name')->paginate(20);

        return response()->json([
            'data' => $methods->items(),
            'meta' => [
                'current_page' => $methods->currentPage(),
                'last_page' => $methods->lastPage(),
                'total' => $methods->total(),
            ],
        ]);
    }
    // Suppression
    public function destroy($id)
    {
        ShippingMethod::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Méthode supprimée'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FRONTEND : Récupération dynamique
    |--------------------------------------------------------------------------
    */

    public function getByCity(Request $request)
    {
        // Valider les params
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'zone_id' => 'nullable|exists:zones,id'
        ]);

        // Récupérer la ville
        $city = City::findOrFail($request->city_id);

        // Récupérer la zone si elle est fournie, sinon null
        $zone = $request->zone_id ? Zone::where('id', $request->zone_id)
            ->where('city_id', $city->id)
            ->first() : null;

        // Si zone_id fourni mais aucune zone trouvée → renvoyer tableau vide
        if ($request->zone_id && !$zone) {
            return response()->json([
                'data' => [],
                'message' => 'Zone introuvable pour cette ville'
            ]);
        }

        // Shipping methods pour la ville
        $methods = ShippingMethod::where('city_id', $city->id)
            ->where('active', true)
            ->get();

        $response = [];

        foreach ($methods as $method) {
            // Livraison gratuite
            if ($method->is_free) {
                $price = 0;
            } else {
                $price = $method->base_price;

                // Si type distance, calculer distance par rapport à la zone
                if ($method->type === 'distance') {
                    if (!$zone) continue; // skip si pas de zone

                    $distance = $this->calculateDistance(
                        3.8480,   // latitude entrepôt
                        11.5021,  // longitude entrepôt
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


    /*
    |--------------------------------------------------------------------------
    | Distance Haversine
    |--------------------------------------------------------------------------
    */

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
