<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Image extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'alt',
        'src'
    ];

    protected $appends = [
        'url',
        'thumb_url',
        'medium_url',
    ];

    /* ================= MEDIA CONVERSIONS ================= */

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->format('webp')
            ->quality(80)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(493)
            ->format('webp')
            ->quality(85)
            ->nonQueued();
    }

    /* ================= ACCESSORS ================= */

    public function getUrlAttribute()
    {
        return $this->getFirstMediaUrl('default');
    }

    public function getThumbUrlAttribute()
    {
        return $this->getFirstMediaUrl('default', 'thumb');
    }

    public function getMediumUrlAttribute()
    {
        return $this->getFirstMediaUrl('default', 'medium');
    }
}
