<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryCategory extends Model
{
    protected $fillable = ['name', 'slug', 'meta_title', 'meta_description'];

    public function images()
    {
        return $this->hasMany(GalleryImage::class, 'category_id');
    }
}
