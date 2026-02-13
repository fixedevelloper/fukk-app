<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = [
        'customer_id',
        'product_id',
    ];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->withDefault();
    }
}
