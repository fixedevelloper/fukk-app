<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountProductCollection extends Model
{
    protected $table = 'ec_discount_product_collections';

    protected $fillable = [
        'discount_id',
        'product_collection_id',
    ];

    public function productCollections(): BelongsTo
    {
        return $this->belongsTo(ProductCollection::class, 'product_collection_id')->withDefault();
    }
}
