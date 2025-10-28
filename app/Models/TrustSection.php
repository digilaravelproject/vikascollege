<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustSection extends Model
{
    protected $fillable = ['title', 'slug', 'content', 'pdf_path', 'status'];

    public function images()
    {
        return $this->hasMany(TrustSectionImage::class);
    }
}
