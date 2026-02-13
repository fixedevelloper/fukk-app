<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreOrderWithOrderDateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'status'         => $this->status,
            'total_amount'   => (float) $this->total_amount,
            'payment_status' => $this->payment_status,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'store' => [
                'id'   => $this->store?->id,
                'name' => $this->store?->name,
                'slug' => $this->store?->slug ?? Str::slug($this->store?->name),
            ],

            'order' => [
        'id'         => $this->order?->id,
                'created_at' => optional($this->order?->created_at)->format('Y-m-d H:i'),
                'status'     => $this->order?->status,
         'payment_status'     => $this->order?->payment_status,
                'amount'     => (float) $this->order?->amount,

                'customer' => $this->when(
        $this->order && $this->order->relationLoaded('customer'),
        fn () => [
            'id'    => $this->order->user->id,
            'name'  => $this->order->user->first_name.' '.$this->order->user->last_name,
            'email' => $this->order->user->email,
        ]
    ),

                'address' => $this->when(
        $this->order && $this->order->relationLoaded('address'),
        fn () => [
            'id'      => $this->order->address?->id,
            'name'    => $this->order->address?->name,
            'phone'   => $this->order->address?->phone,
            'email'   => $this->order->address?->email,
            'country' => $this->order->address?->country,
            'state'   => $this->order->address?->state,
            'city'    => $this->order->address?->city,
            'address' => $this->order->address?->address,
        ]
    ),
            ],

            'products' => OrderProductResource::collection(
        $this->whenLoaded('orderProducts')
    ),
        ];
    }
}

