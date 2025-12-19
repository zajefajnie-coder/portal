<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profession',
        'city',
        'bio',
        'phone',
        'social_links',
        'avatar',
        'is_approved',
        'is_banned',
        'banned_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',
            'is_approved' => 'boolean',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
        ];
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function publicPortfolios()
    {
        return $this->hasMany(Portfolio::class)->where('is_public', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true)->where('is_banned', false);
    }

    public function scopeNotBanned($query)
    {
        return $query->where('is_banned', false);
    }
}



