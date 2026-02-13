<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorMessage extends Model
{

    protected $fillable = [
        'store_id',
        'customer_id',
        'name',
        'email',
        'content',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
