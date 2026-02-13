<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCustomer extends Model
{
    protected $table = 'ec_discount_customers';

    protected $fillable = [
        'discount_id',
        'customer_id',
    ];

    public function customers(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }
}
