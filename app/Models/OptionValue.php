<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{
    protected $table = 'ec_option_value';

    protected $fillable = [
        'option_id',
        'option_value',
        'affect_price',
        'affect_type',
        'order',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'option_id');
    }

    protected function formatPrice(): ProductAttribute
    {
        return ProductAttribute::get(fn () => format_price($this->price));
    }

    protected function price(): ProductAttribute
    {
        return ProductAttribute::get(function (): float|int {
            $option = $this->option;

            if ($option->option_type == Field::class) {
                return 0;
            }

            $product = $option->product;

            return $this->affect_type == 0 ? $this->affect_price : (floatval($this->affect_price) * $product->original_price) / 100;
        });
    }
}
