<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude', 'city_id'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
