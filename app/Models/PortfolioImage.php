<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'image_path',
        'thumbnail_path',
        'order',
        'alt_text',
        'is_reported',
        'report_reason',
        'is_hidden',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_reported' => 'boolean',
            'is_hidden' => 'boolean',
        ];
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeReported($query)
    {
        return $query->where('is_reported', true);
    }
}



