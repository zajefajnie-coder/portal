<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_public',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'views' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PortfolioImage::class)->orderBy('order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'portfolio_tag')->withTimestamps();
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}



