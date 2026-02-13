<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug ?? null,

            'description' => $this->description,
            'content' => $this->content,

            'status' => $this->status,
            'is_verified' => ! is_null($this->vendor_verified_at),

            'contact' => [
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'neighborhood' => $this->neighborhood,
                'zip_code' => $this->zip_code,
            ],

            'location' => [
                'city' => $this->whenLoaded('city', function () {
                    return [
                        'id' => $this->city->id,
                        'name' => $this->city->name,
                        'latitude' => $this->city->latitude,
                        'longitude' => $this->city->longitude,
                    ];
                }),
            ],

            'vendor' => $this->whenLoaded('vendor', function () {
                return [
                    'id' => $this->vendor->id,
                    'name' => $this->vendor->name,
                    'email' => $this->vendor->email,
                ];
            }),

            /* ================= MEDIA ================= */

            'logo' => new ImageResource(
                $this->whenLoaded('logo')
            ),

            'cover_image' => new ImageResource(
                $this->whenLoaded('coverImage')
            ),

            /* ================= STATS ================= */

            'stats' => [
                'products_count' => $this->whenCounted('products'),
                'orders_count' => $this->whenCounted('orders'),
            ],

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
