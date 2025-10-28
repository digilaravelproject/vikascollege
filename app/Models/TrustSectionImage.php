<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustSectionImage extends Model
{
    protected $fillable = ['trust_section_id', 'image_path'];

    public function section()
    {
        return $this->belongsTo(TrustSection::class);
    }
}
