<?php


namespace App\Http\Controllers\Admin;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ZoneController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN CRUD
    |--------------------------------------------------------------------------
    */

    // Liste des zones
    public function index()
    {
        return response()->json([
            'data' => Zone::with('city')->latest()->get()
        ]);
    }

    // Création
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $zone = Zone::create($request->all());

        return response()->json([
            'message' => 'Zone créée avec succès',
            'data' => $zone
        ]);
    }

    // Afficher une zone
    public function show($id)
    {
        return response()->json([
            'data' => Zone::with('city')->findOrFail($id)
        ]);
    }

    // Mise à jour
    public function update(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);

        $zone->update($request->all());

        return response()->json([
            'message' => 'Zone mise à jour avec succès',
            'data' => $zone
        ]);
    }

    // Suppression
    public function destroy($id)
    {
        Zone::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Zone supprimée'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FRONTEND : Récupérer les zones par ville
    |--------------------------------------------------------------------------
    */

    public function getByCity(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id'
        ]);

        $zones = Zone::where('city_id', $request->city_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $zones
        ]);
    }
}
