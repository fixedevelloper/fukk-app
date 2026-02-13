<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ProductAttribute extends Model
{


    protected $fillable = [
        'title',
        'slug',
        'color',
        'order',
        'attribute_set_id',
        'image',
        'is_default',
    ];

    public function getAttributeSetIdAttribute($value)
    {
        return $value;
    }

    public function productAttributeSet()
    {
        return $this->belongsTo(ProductAttributeSet::class, 'attribute_set_id');
    }

    public function getGroupIdAttribute($value)
    {
        return $value;
    }

    protected static function booted(): void
    {
        self::saving(function (self $model) {
            $model->slug =Helper::str_slug($model->title);
        });

        static::deleted(
            fn (ProductAttribute $productAttribute) => $productAttribute->productVariationItems()->delete()
        );
    }

    public function productVariationItems()
    {
        return $this->hasMany(ProductVariationItem::class, 'attribute_id');
    }

    public function getAttributeStyle(?ProductAttributeSet $attributeSet = null,$productVariations = [])
    {
        if ($attributeSet && $attributeSet->use_image_from_product_variation) {
            foreach ($productVariations as $productVariation) {
                $attribute = $productVariation->productAttributes->where('attribute_set_id', $attributeSet->id)->first();
                if ($attribute && $attribute->id == $this->id && ($image = $productVariation->product->image)) {
                    return 'background-image: url(' . RvMedia::getImageUrl($image) . '); background-size: cover; background-repeat: no-repeat; background-position: center;';
                }
            }
        }

        if ($this->image) {
            return 'background-image: url(' . RvMedia::getImageUrl($this->image) . '); background-size: cover; background-repeat: no-repeat; background-position: center;';
        }

        return 'background-color: ' . ($this->color ?: '#000') . ' !important;';
    }
}
