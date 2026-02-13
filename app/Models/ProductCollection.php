<?php
namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCollection extends Model
{


    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status',
        'is_featured',
    ];


    protected static function booted(): void
    {

        static::deleted(function (ProductCollection $collection) {
            $collection->discounts()->detach();
        });
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class, 'ec_discount_customers', 'customer_id', 'id');
    }

    public function products()
    {
        return $this
            ->belongsToMany(
                Product::class,
                'product_collection_products',
                'product_collection_id',
                'product_id'
            )
            ->where('is_variation', 0);
    }

    public function promotions()
    {
        return $this
            ->belongsToMany(Discount::class, 'ec_discount_product_collections', 'product_collection_id')
            ->where('type', DiscountTypeEnum::PROMOTION)
            ->where('start_date', '<=', Carbon::now())
            ->where('target', DiscountTargetEnum::PRODUCT_COLLECTIONS)
            ->where(function ($query) {
                return $query
                    ->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::now());
            })
            ->where('product_quantity', 1);
    }
}
