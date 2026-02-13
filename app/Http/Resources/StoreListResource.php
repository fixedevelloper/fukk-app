<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class StoreListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => new ImageResource($this->whenLoaded('logo')),
            'city' => $this->city?->name,
            'is_verified' => ! is_null($this->vendor_verified_at),
        ];
    }
}

