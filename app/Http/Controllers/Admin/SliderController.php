<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\BannerResource;
use App\Http\Resources\SliderResource;
use App\Models\Banner;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SliderController extends Controller
{
    public function index(Request $request)
    {
        $sliders = Slider::with('image')
            ->where('is_active', true)
            ->orderBy('position')
            ->paginate(20);

        return SliderResource::collection($sliders);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'image_id' => 'required|exists:images,id',
            'href' => 'nullable|string',
            'description' => 'nullable|string',
            'btn_text' => 'nullable|string',
            'position' => 'nullable|integer',
            'is_active' => 'required|string',
        ]);
        $validated['is_active']= $request->is_active=='true';
        $slider = Slider::create([
            'title'      => $validated['title'],
            'btn_text'   => $validated['btn_text'],
            'image_id'   => $validated['image_id'],
            'href'       => $validated['href'] ?? null,
            'description'=> $validated['description'] ?? null,
            'position'   => $validated['position'] ?? null,
            'is_active'  =>  $validated['is_active'],
        ]);

        return new SliderResource($slider->load('image'));
    }

    public function update(Request $request, Slider $slider)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'image_id' => 'required|exists:images,id',
            'href' => 'nullable|string',
            'description' => 'nullable|string',
            'position' => 'nullable|integer',
            'btn_text' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        logger($validated);
        $validated['is_active']= $request->is_active=='true';
        $slider->update($validated);

        return new SliderResource($slider->load('image'));
    }
    public function show(Slider $slider)
    {
        $slider->load('image');

        return new SliderResource($slider);
    }

    public function toggle(Slider $slider)
    {
        $slider->is_active = !$slider->is_active;
        $slider->save();

        return response()->json([
            'status' => 'success',
            'is_active' => $slider->is_active,
        ]);
    }
}

