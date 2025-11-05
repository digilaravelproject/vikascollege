<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicCalendar extends Model
{
    protected $fillable = [
        'title', 'slug', 'event_datetime', 'image', 'description', 'link_href', 'status',
        'meta_title', 'meta_description'
    ];

    protected $casts = [
        'event_datetime' => 'datetime',
        'status' => 'boolean',
    ];
}
