<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{

    protected $fillable = [
        'title',
        'country',
    ];

    protected static function booted(): void
    {
        static::deleted(function (Shipping $shipping) {
            $shipping->rules()->each(fn (ShippingRule $rule) => $rule->delete());
        });
    }

    public function rules(): HasMany
    {
        return $this->hasMany(ShippingRule::class, 'shipping_id');
    }
}
