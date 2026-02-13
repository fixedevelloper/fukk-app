<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'mp_customer_revenues';

    protected $fillable = [
        'customer_id',
        'product_id',
        'star',
        'status',
        'comment',
    ];

    protected $casts = [
        'type' => RevenueTypeEnum::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withDefault();
    }

    public function currencyRelation(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency', 'title')->withDefault();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function getDescriptionTooltipAttribute(): string
    {
        if (! $this->description) {
            return '';
        }

        return Html::tag('span', '<i class="fa fa-info-circle text-info"></i>', [
            'class' => 'ms-1',
            'data-bs-toggle' => 'tooltip',
            'data-bs-original-title' => $this->description,
            'title' => $this->description,
        ]);
    }
}
