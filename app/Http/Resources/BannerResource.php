<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'href' => $this->href,
            'is_active' => $this->is_active,
            'image' => new ImageResource($this->whenLoaded('image')),
            'created_at' => $this->created_at,
        ];
    }

}
