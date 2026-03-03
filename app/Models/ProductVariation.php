<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{

    protected $fillable = [
        'product_id',
        'configurable_product_id',
        'is_default',
        'price',
        'sale_price',
        'stock'
    ];

    protected $casts = [
        'price' => 'float',
        'sale_price' => 'float',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items()
    {
        return $this->hasMany(ProductVariationItem::class, 'variation_id');
    }
    public function getFinalPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }
}
