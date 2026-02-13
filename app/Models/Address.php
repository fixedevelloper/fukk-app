<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    protected $fillable = [
        'name',
        'phone',
        'email',
        'state',
        'city',
        'address',
        'zip_code',
        'customer_id',
        'is_default',
    ];
}
