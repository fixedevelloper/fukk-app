<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRule extends Model
{

    protected $fillable = [
        'tax_id',
        'country',
        'state',
        'city',
        'zip_code',
        'percentage',
        'priority',
        'is_enabled',
    ];

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class)->withDefault();
    }
}
