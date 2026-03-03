<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,

            'short_description' => $this->short_description,
            'description' => $this->description,

            'sku' => $this->sku,
            'reference' => $this->reference,

            'price' => (float) $this->price,
            'sale_price' => (float) ($this->sale_price ?? $this->price),
            'discount_price' => (float) $this->discount_price,

            'quantity' => $this->quantity,
            'stock_status' => $this->stock_status,
            'is_out_of_stock' => $this->isOutOfStock(),

            'dimensions' => [
                'length' => $this->length,
                'wide'   => $this->wide,
                'height' => $this->height,
                'weight' => $this->weight,
            ],

            'status' => $this->status,
            'is_featured' => (bool) $this->is_featured,

            /* ===================== RELATIONS ===================== */

            'store' => new StoreResource($this->whenLoaded('store')),
            'brand' => new BrandResource($this->whenLoaded('brand')),

            'image' => $this->whenLoaded('featuredImage', fn () =>
            new ImageResource($this->featuredImage)
            ),

            'images' => ImageResource::collection(
                $this->whenLoaded('images')
            ),

            'categories' => CategoryResource::collection(
                $this->whenLoaded('categories')
            ),

            'labels' => LabelResource::collection(
                $this->whenLoaded('labels')
            ),

            'collections' => ProductCollectionResource::collection(
                $this->whenLoaded('collections')
            ),

            'related_products' => ProductResource::collection(
                $this->whenLoaded('relatedProducts')
            ),
            'variations' => $this->whenLoaded('variations', function () {
                return $this->variations->map(function ($variation) {
                    return [
                        'id' => $variation->id,
                        'price' => (float) $variation->price,
                        'stock' => $variation->stock,
                        'sku' => $variation->sku,
                        'is_default' => (bool) $variation->is_default,

                        'attributes' => $variation->items->map(function ($value) {
                            return [
                                'attribute_id' => $value->attribute->id,
                                'attribute_name' => $value->attribute->title,
                                'value_id' => $value->id,
                                'value_name' => $value->title,
                            ];
                        }),
                    ];
                });
            }),
            'has_variations' => $this->variations->isNotEmpty(),
            'default_variation_id' => optional(
                $this->variations->firstWhere('is_default', 1)
            )->id,
            'min_price' => $this->variations->min('final_price'),
            'max_price' => $this->variations->max('final_price'),

            /* ===================== META ===================== */

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
