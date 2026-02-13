<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /* ================= BASIC ================= */
            'id'           => $this->id,
            'token'        => $this->token,
            'status'       => $this->status,
            'is_confirmed' => (bool) $this->is_confirmed,
            'is_finished'  => (bool) $this->is_finished,

            /* ================= AMOUNTS ================= */
            'amount'              => (float) $this->amount,
            'sub_total'           => (float) $this->sub_total,
            'tax_amount'          => (float) $this->tax_amount,
            'shipping_amount'     => (float) $this->shipping_amount,
            'discount_amount'     => (float) $this->discount_amount,
            'discount_description'=> $this->discount_description,

            /* ================= SHIPPING ================= */
            'shipping_option' => $this->shipping_option,
            'shipping_method' => $this->shipping_method,

            /* ================= CUSTOMER ================= */
            'customer' => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            /* ================= STORE ORDERS ================= */
            'store_orders' => StoreOrderResource::collection(
                $this->whenLoaded('storeOrders')
            ),

            /* ================= ADDRESS ================= */
            'address' => $this->whenLoaded('address', function () {
                return [
                    'id'      => $this->address->id,
                    'name'    => $this->address->name,
                    'phone'   => $this->address->phone,
                    'email'   => $this->address->email,
                    'country' => $this->address->country,
                    'state'   => $this->address->state,
                    'city'    => $this->address->city,
                    'address' => $this->address->address,
                ];
            }),

            /* ================= PAYMENT ================= */
            'currency_id' => $this->currency_id,
            'payment_id'  => $this->payment_id,

            /* ================= DATES ================= */
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

