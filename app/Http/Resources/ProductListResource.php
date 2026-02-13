<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => (float) $this->price,
            'sale_price' => (float) ($this->sale_price ?? $this->price),
            'image' => $this->whenLoaded('featuredImage', fn () =>
            new ImageResource($this->featuredImage)
            ),
            'is_out_of_stock' => $this->isOutOfStock(),
        ];
    }
}
