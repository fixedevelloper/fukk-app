<?php


namespace App\Http\Controllers\Admin;


use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BrandController
{
    public function index(Request $request)
    {
        logger('icic');
        $query = Brand::with('image');

        // 🔎 Recherche
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // 🔘 Filtre actif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $banners = $query
            ->latest()
            ->paginate($request->get('per_page', 10));

        logger($banners);
        return BrandResource::collection($banners);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image_id' => 'required|exists:images,id',
            'is_active' => 'boolean',
        ]);

        $banner = Brand::create($validated);

        return new BrandResource($banner->load('image'));
    }

    /**
     * Display the specified resource.
     * @param Brand $brand
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Brand $brand
     */
    public function update(Request $request, Brand $brand)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param Brand $brand
     */
    public function destroy(Brand $brand)
    {
        //
    }
}
