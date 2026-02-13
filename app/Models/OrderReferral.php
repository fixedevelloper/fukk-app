<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReferral extends Model
{

    protected $fillable = [
        'ip',
        'landing_domain',
        'landing_page',
        'landing_params',
        'referral',
        'gclid',
        'fclid',
        'utm_source',
        'utm_campaign',
        'utm_medium',
        'utm_term',
        'utm_content',
        'referrer_url',
        'referrer_domain',
        'order_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withDefault();
    }
}
