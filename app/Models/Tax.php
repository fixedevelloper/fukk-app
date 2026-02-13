<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{

    protected $fillable = [
        'title',
        'percentage',
        'priority',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    protected static function booted(): void
    {
        static::deleted(function (Tax $tax) {
            $tax->products()->detach();
            $tax->rules()->delete();
        });
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'ec_tax_products', 'tax_id', 'product_id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(TaxRule::class);
    }

    protected function defaultTitle(): ProductAttribute
    {
        return ProductAttribute::get(fn () => $this->is_default ? (' - ' . trans('plugins/ecommerce::tax.default')) : '');
    }

    protected function titleWithPercentage(): ProductAttribute
    {
        return ProductAttribute::get(fn () => $this->title . ' (' . $this->percentage . '%)' . $this->default_title);
    }

    protected function isDefault(): ProductAttribute
    {
        return ProductAttribute::get(fn () => $this->id == get_ecommerce_setting('default_tax_rate'));
    }
}
