<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventItem extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'image',
        'event_date',
        'venue',
        'link', // <--- Added here
        'short_description',
        'full_content',
        'status',
        'preference_order',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }
}
