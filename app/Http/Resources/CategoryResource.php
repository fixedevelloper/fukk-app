<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,

            'short_description' => $this->short_description,
            'description'       => $this->description,

            'status'      => $this->status,
            'is_featured' => (bool) $this->is_featured,
            'order'       => $this->order,

            /* ================= IMAGE ================= */

            'image' => new ImageResource(
                $this->whenLoaded('image')
            ),

            /* ================= HIERARCHY ================= */

            'parent_id' => $this->parent_id,

            'children' => CategoryResource::collection(
                $this->whenLoaded('children')
            ),

            /* ================= STATS ================= */

            'products_count' => $this->whenCounted('products'),

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
