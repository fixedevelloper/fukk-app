<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountProduct extends Model
{


    protected $fillable = [
        'discount_id',
        'product_id',
    ];

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id')->withDefault();
    }
}
