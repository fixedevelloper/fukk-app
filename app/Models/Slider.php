<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'title',
        'image_id',
        'href',
        'position',
        'description',
        'is_active',
        'btn_text'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
