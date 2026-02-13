<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,

            /* ================= IMAGE ================= */
            'image' => new ImageResource(
                $this->whenLoaded('image')
            ),

            /* ================= PRODUCTS ================= */
            'products' => ProductResource::collection(
                $this->whenLoaded('products')
            ),

            /* ================= META ================= */
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
