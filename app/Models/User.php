<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'bio',
        'location',
        'avatar',
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
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function castings()
    {
        return $this->hasMany(Casting::class);
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && $this->avatar !== 'default-avatar.jpg') {
            return asset('storage/uploads/avatars/' . $this->avatar);
        }
        return asset('images/default-avatar.jpg');
    }
}


