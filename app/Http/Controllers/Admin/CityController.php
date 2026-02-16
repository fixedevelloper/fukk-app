<?php


namespace App\Http\Controllers\Admin;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CityController extends Controller
{
    /**
     * Récupérer toutes les villes avec pagination
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $cities = City::orderBy('name')->paginate($perPage);

        return response()->json([
            'data' => $cities->items(),
            'meta' => [
                'current_page' => $cities->currentPage(),
                'last_page' => $cities->lastPage(),
                'total' => $cities->total(),
            ],
        ]);
    }

    /**
     * Créer une nouvelle ville
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $city = City::create([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['data' => $city], 201);
    }

    /**
     * Récupérer une ville spécifique
     */
    public function show($id)
    {
        $city = City::findOrFail($id);

        return response()->json(['data' => $city]);
    }

    /**
     * Mettre à jour une ville
     */
    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $city->update([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['data' => $city]);
    }

    /**
     * Supprimer une ville
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json(['message' => 'City deleted successfully.']);
    }
    /**
     * Récupérer les zones
     */
    public function zoneByCityId($id)
    {
        $city = City::findOrFail($id);

        return response()->json(['data' => $city->zones]);
    }


}

