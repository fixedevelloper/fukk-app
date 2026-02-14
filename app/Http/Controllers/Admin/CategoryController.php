<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use function Illuminate\Support\query;

class CategoryController
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Category::with(['image', 'children', 'products']);

        // 🔎 Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 📌 Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 📄 Pagination
        $perPage = $request->get('limit', 10);

        $categories = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return CategoryResource::collection($categories);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return
     */
    public function store(Request $request)
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

    /**
     * Display the specified resource.
     * @param Category $category
     */
    public function show(Category $category)
    {

        return new CategoryResource($category->load('image'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Category $category
     */
    public function update(Request $request, Category $category)
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
        return $category->update($data);
    }

    /**
     * Remove the specified resource from storage.
     * @param Category $category
     */
    public function destroy(Category $category)
    {
        //
    }
    public function categorieParent(Request $request)
    {
        $categories = Category::query()
            ->select('id', 'name', 'parent_id', 'slug', 'order')
            ->get();

        logger($categories);
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

}
