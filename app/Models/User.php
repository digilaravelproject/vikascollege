<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * User ke naam se initials (e.g., "Super Admin" -> "SA") banata hai.
     */
    public function getInitialsAttribute()
    {
        $name = $this->name; // User ka poora naam

        // Naam ko 2 hisson mein todo (max 2 words)
        $words = explode(' ', $name, 2);

        // Pehle shabd ka pehla letter
        $initials = mb_substr($words[0], 0, 1);

        // Agar doosra shabd hai, toh uska pehla letter
        if (isset($words[1])) {
            $initials .= mb_substr($words[1], 0, 1);
        }

        return strtoupper($initials);
    }
}
