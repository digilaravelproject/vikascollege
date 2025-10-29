<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['slug', 'title', 'content', 'image', 'pdf', 'menu_id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
