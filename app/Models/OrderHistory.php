<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{

    protected $fillable = [
        'action',
        'description',
        'user_id',
        'order_id',
        'extras',
    ];

    protected $casts = [
        'action' => OrderHistoryActionEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id')->withDefault();
    }

    protected function extras(): ProductAttribute
    {
        return ProductAttribute::get(fn (?string $value): array => json_decode($value, true) ?: []);
    }
}
