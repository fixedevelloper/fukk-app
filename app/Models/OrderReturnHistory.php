<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturnHistory extends Model
{

    protected $fillable = [
        'user_id',
        'order_return_id',
        'action',
        'description',
        'reason',
    ];

    protected $casts = [
        'action' => OrderReturnHistoryActionEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class, 'order_return_id');
    }
}
