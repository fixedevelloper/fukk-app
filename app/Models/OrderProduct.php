<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_product';
    protected $fillable = [
        'store_order_id',
        'product_id',
        'product_name',
        'qty',
        'price',
        'tax_amount',
        'options',
        'product_options',
        'restock_quantity',
        'downloaded_at',
    ];

    protected $casts = [
        'options' => 'json',
        'product_options' => 'json',
        'downloaded_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    public function order()
    {
        return $this->belongsTo(Order::class)->withDefault();
    }

    public function productFiles(): HasMany
    {
        return $this->hasMany(ProductFile::class, 'product_id', 'product_id');
    }

    public function totalFormat(): ProductAttribute
    {
        return ProductAttribute::get(fn () => format_price($this->price * $this->qty));
    }

    public function productImageUrl(): ProductAttribute
    {
        return ProductAttribute::get(fn () => RvMedia::getImageUrl($this->product_image, 'thumb', default: RvMedia::getDefaultImage()));
    }

    protected function amountFormat(): ProductAttribute
    {
        return ProductAttribute::get(fn () => format_price($this->price));
    }

    protected function productFileExternalCount(): ProductAttribute
    {
        return ProductAttribute::get(fn () => $this->productFiles->filter(fn (ProductFile $file) => $file->is_external_link)->count());
    }

    protected function productFileInternalCount(): ProductAttribute
    {
        return ProductAttribute::get(fn () => $this->productFiles->filter(fn (ProductFile $file) => ! $file->is_external_link)->count());
    }

    public function isTypeDigital(): bool
    {
        return isset($this->attributes['product_type']) && $this->attributes['product_type'] == ProductTypeEnum::DIGITAL;
    }

    protected function downloadToken(): ProductAttribute
    {
        return ProductAttribute::get(fn () => $this->isTypeDigital() ? ($this->order->id . '-' . $this->order->token . '-' . $this->id) : null);
    }

    protected function downloadHash(): ProductAttribute
    {
        return ProductAttribute::get(fn () => $this->download_token ? Hash::make($this->download_token) : null);
    }

    protected function downloadHashUrl(): ProductAttribute
    {
        return ProductAttribute::get(fn () => $this->download_hash ? route('public.digital-products.download', [
            'id' => $this->id,
            'hash' => $this->download_hash,
        ]) : null);
    }

    protected function downloadExternalUrl(): ProductAttribute
    {
        return ProductAttribute::get(fn () => $this->download_hash ? route('public.digital-products.download', [
            'id' => $this->id,
            'hash' => $this->download_hash,
            'external' => true,
        ]) : null);
    }

    protected function priceWithTax(): ProductAttribute
    {
        return ProductAttribute::get(fn () => $this->price + $this->tax_amount);
    }

    protected function totalPriceWithTax(): ProductAttribute
    {
        return ProductAttribute::get(fn () => $this->price_with_tax * $this->qty);
    }

    public function productOptionsImplode(): ProductAttribute
    {
        return ProductAttribute::get(function () {
            if (! $this->product_options) {
                return '';
            }

            $options = $this->product_options;

            return '(' . implode(', ', Arr::map(Arr::get($options, 'optionInfo'), function ($item, $key) use ($options) {
                return implode(': ', [
                    $item,
                    Arr::get($options, "optionCartValue.$key.0.option_value"),
                ]);
            })) . ')';
        });
    }
}
