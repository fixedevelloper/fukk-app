<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'description',
        'order',
        'status',
        'is_featured',
        'icon',
        'image_id'
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function products()
    {
        return $this
            ->belongsToMany(
                Product::class,
                'product_category',
                'category_id',
                'product_id'
            );
    }

    public function parent()
    {
        return $this
            ->belongsTo(Category::class, 'parent_id')
            ->whereNot('parent_id', $this->getKey())
            ->withDefault();
    }

    public function children()
    {
        return $this
            ->hasMany(Category::class, 'parent_id')
            ->whereNot('id', $this->getKey());
    }

    public function activeChildren()
    {
        return $this
            ->children()
            ->wherePublished()
            ->orderBy('order')
            ->with(['slugable', 'activeChildren']);
    }
}
