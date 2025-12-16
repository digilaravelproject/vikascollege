<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile_prefix',
        'mobile_no',
        'discipline',
        'level',
        'programme',
        'authorised_contact',
        'status',
        'verified_at'
    ];

    protected $casts = [
        'authorised_contact' => 'boolean',
        'verified_at' => 'datetime',
    ];
}
