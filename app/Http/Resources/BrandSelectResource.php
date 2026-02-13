<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class BrandSelectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
