<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class CategoryMenuResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'children' => CategoryMenuResource::collection(
                $this->whenLoaded('children')
            ),
        ];
    }
}
