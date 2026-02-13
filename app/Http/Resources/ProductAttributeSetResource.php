<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeSetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'display_layout' => $this->display_layout,
            'is_searchable' => (bool) $this->is_searchable,
            'is_comparable' => (bool) $this->is_comparable,
            'is_use_in_product_listing' => (bool) $this->is_use_in_product_listing,
            'status' => $this->status,
            'order' => $this->order,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            // Optionnel : compter le nombre d'attributs liés
            'attributes_count' => $this->attributes?->count() ?? 0,
        ];
    }
}
