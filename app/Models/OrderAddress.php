<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{


    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'state',
        'city',
        'address',
        'zip_code',
        'order_id',
        'type',
    ];

    public $timestamps = false;

    protected $casts = [
        'type' => OrderAddressTypeEnum::class,
    ];

    protected function avatarUrl(): ProductAttribute
    {
        return ProductAttribute::get(function () {
            try {
                return (new Avatar())->create($this->name)->toBase64();
            } catch (Exception) {
                return RvMedia::getDefaultImage();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withDefault();
    }
}
