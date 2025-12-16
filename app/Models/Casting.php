<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Casting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'casting_date',
        'required_roles',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'casting_date' => 'date',
            'required_roles' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


