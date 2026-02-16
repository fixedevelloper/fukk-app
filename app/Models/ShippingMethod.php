<?php

namespace App\Models;


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'description',
        'type',         // 'fixed' ou 'distance'
        'base_price',
        'price_per_km',
        'city_id',
        'is_free',
        'active',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'active' => 'boolean',
        'base_price' => 'float',
        'price_per_km' => 'float',
    ];

    /**
     * Shipping method belongs to a city (nullable for default shipping)
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
