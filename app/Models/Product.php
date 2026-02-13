<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    const OUT_OF_STOCK = 'OUT_OF_STOCK';
    const IN_OF_STOCK  = 'IN_OF_STOCK';

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'sku',
        'reference',
        'quantity',
        'image_id',
        'allow_checkout_when_out_of_stock',
        'with_storehouse_management',
        'is_featured',
        'brand_id',
        'is_variation',
        'sale_type',
        'price',
        'sale_price',
        'discount_price',
        'start_date',
        'end_date',
        'length',
        'wide',
        'height',
        'weight',
        'tax_id',
        'created_by_id',
        'created_by_type',
        'status',
        'order',
        'store_id',
        'stock_status',
    ];
    protected $casts = [
        'price' => 'float',
        'sale_price' => 'float',
        'quantity' => 'integer',
        'is_featured' => 'boolean',
        'allow_checkout_when_out_of_stock' => 'boolean',
    ];
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /* ===================== RELATIONS ===================== */

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function featuredImage()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function images()
    {
        return $this->belongsToMany(
            Image::class,
            'product_image'
        );
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'product_category'
        );
    }

    public function labels()
    {
        return $this->belongsToMany(
            Label::class,
            'label_products'
        );
    }

    public function collections()
    {
        return $this->belongsToMany(
            ProductCollection::class,
            'product_collection_products'
        );
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_related_relations',
            'from_product_id',
            'to_product_id'
        );
    }

    public function productAttributeSets()
    {
        return $this->belongsToMany(
            ProductAttributeSet::class,
            'product_with_attribute_set', // nom de la table pivot
            'product_id',                // clé étrangère pour Product dans pivot
            'attribute_set_id'           // clé étrangère pour ProductAttributeSet dans pivot
        );
    }


    /* ===================== BUSINESS LOGIC ===================== */

    public function isOutOfStock(): bool
    {
        if (! $this->with_storehouse_management) {
            return $this->stock_status === self::OUT_OF_STOCK;
        }

        return $this->quantity <= 0 && ! $this->allow_checkout_when_out_of_stock;
    }

}
