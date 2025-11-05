<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['student_name', 'student_image', 'testimonial_text', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];
}
