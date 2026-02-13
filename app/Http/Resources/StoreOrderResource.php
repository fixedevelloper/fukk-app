<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'status'        => $this->status,
            'total_amount'  => $this->total_amount,
            'payment_status'=> $this->payment_status,
            'store'         => [
                'id'   => $this->store->id,
                'name' => $this->store->name,
                'slug' => $this->store->slug,
            ],
            'products'      => OrderProductResource::collection(
                $this->orderProducts
            ),
        ];
    }
}
