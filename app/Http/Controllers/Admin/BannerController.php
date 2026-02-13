<?php


namespace App\Http\Controllers\Admin;

use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::with('image');

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

        return BannerResource::collection($banners);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_id' => 'required|exists:images,id',
            'href' => 'nullable|string',
            'placement' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $banner = Banner::create($validated);

        return new BannerResource($banner->load('image'));
    }
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_id' => 'required|exists:images,id',
            'href' => 'nullable|string',
            'placement' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active']= $request->is_active=='true';
        $banner->update($validated);

        return new BannerResource($banner->load('image'));
    }
    public function show(Banner $banner)
    {

        $banner->load('image');
        return response()->json(new BannerResource($banner));
    }
    public function toggleActive(Banner $banner)
    {
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return response()->json(['status' => 'success', 'is_active' => $banner->is_active]);
    }

}
