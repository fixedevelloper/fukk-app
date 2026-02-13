<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'image_id',
        'href',
        'placement',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
