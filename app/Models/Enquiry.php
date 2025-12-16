<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile_prefix',
        'mobile_no',
        'level',
        'discipline',
        'programme',
        'message',
        'authorised_contact',
        'status',
        'verified_at'
    ];

    protected $casts = [
        'authorised_contact' => 'boolean',
        'verified_at' => 'datetime',
    ];
}
