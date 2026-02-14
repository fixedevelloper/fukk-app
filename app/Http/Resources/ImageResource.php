<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'src'  => $this->getFirstMediaUrl('default'),
            'thumb'=> $this->getFirstMediaUrl('default', 'thumb'),
            'medium'=> $this->getFirstMediaUrl('default', 'medium'),
            'alt'  => $this->alt,
        ];
    }
}

