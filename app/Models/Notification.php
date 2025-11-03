<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'icon',
        'title',
        'href',
        'button_name',
        'status',
        'featured',
        'feature_on_top',
        'display_date',
    ];

    protected $casts = [
        'status' => 'boolean',
        'featured' => 'boolean',
        'feature_on_top' => 'boolean',
        'display_date' => 'date',
    ];
}


