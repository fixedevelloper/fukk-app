<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'image_id'
    ];
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
