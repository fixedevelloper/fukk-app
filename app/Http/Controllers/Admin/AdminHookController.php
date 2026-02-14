<?php


namespace App\Http\Controllers\Admin;


use App\Http\Resources\ImageResource;
use App\Http\Resources\ProductAttributeResource;
use App\Http\Resources\ProductAttributeSetResource;
use App\Models\Category;
use App\Models\Image;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeSet;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class AdminHookController extends Controller
{

    public function getAttributes()
    {
        $attributes = ProductAttribute::with('productAttributeSet')
            ->orderBy('attribute_set_id')
            ->orderBy('order')
            ->paginate(20);

        return ProductAttributeResource::collection($attributes);
    }
    public function storeAttribut(Request $request)
    {
        $data = $request->validate([
            'attribute_set_id' => 'required|exists:product_attribute_sets,id',
            'title' => 'required|string|max:120',
            'color' => 'nullable|string|max:50',
            'status' => 'required|in:published,draft',
        ]);

        $data['slug'] = Str::slug($data['title']);

        return ProductAttribute::create($data);
    }
    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'image_id' => 'nullable|exists:images,id',
            'name' => 'required|string|max:120',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $data['slug'] = Str::slug($data['name']);

        if (is_null($data['parent_id'])){
            $data['parent_id'] = 0;
        }
        return Category::create($data);
    }
    public function getAttributSet()
    {
        $attributeSets = ProductAttributeSet::with('attributes')
            ->orderBy('order')
            ->paginate(20);

        return ProductAttributeSetResource::collection($attributeSets);
    }
    public function storeAttributSet(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:120|unique:product_attribute_sets,title',
            'display_layout' => 'required|string',
            'is_searchable' => 'boolean',
            'is_comparable' => 'boolean',
            'is_use_in_product_listing' => 'boolean',
            'status' => 'required|in:published,draft',
        ]);

        $data['slug'] = Str::slug($data['title']);

        return ProductAttributeSet::create($data);
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
}
