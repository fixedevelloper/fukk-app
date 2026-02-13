<?php


namespace App\Http\Controllers\Admin;

use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Store::with('logo','cover_image');

        // 🔎 Recherche
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // 🔘 Filtre actif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $stores = $query
            ->latest()
            ->paginate($request->get('per_page', 10));


        return StoreResource::collection($stores);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        //
    }
}
