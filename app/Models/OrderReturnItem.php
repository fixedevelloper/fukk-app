<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturnItem extends Model
{

    protected $fillable = [
        'order_return_id',
        'order_product_id',
        'product_id',
        'product_name',
        'product_image',
        'qty',
        'price',
        'reason',
        'refund_amount',
    ];

    protected $casts = [
        'reason' => OrderReturnReasonEnum::class,
    ];

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class, 'order_return_id');
    }

    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class, 'order_product_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
