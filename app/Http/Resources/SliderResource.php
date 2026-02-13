<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => new ImageResource($this->whenLoaded('image')),
            'description' => $this->description,
            'position' => $this->position,
            'btn_text'=>$this->btn_text,
            'is_active' => $this->is_active,
        ];
    }
}

