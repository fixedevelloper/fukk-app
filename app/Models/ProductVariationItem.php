<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ProductVariationItem extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'attribute_id',
        'variation_id'
    ];

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }


}
