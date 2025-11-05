<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'meta_title', 'meta_description'
    ];

    public function items()
    {
        return $this->hasMany(EventItem::class, 'category_id');
    }
}
