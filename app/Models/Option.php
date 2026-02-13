<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'ec_options';

    protected $fillable = [
        'name',
        'option_type',
        'required',
        'product_id',
        'order',
    ];

    protected static function booted(): void
    {
        self::deleted(function (Option $option) {
            $option->values()->delete();
        });
    }

    public function values(): HasMany
    {
        return $this
            ->hasMany(OptionValue::class, 'option_id')
            ->orderBy('order');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
