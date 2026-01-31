<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'title',
        'original_url',
        'short_code',
        'clicks',
        'is_active',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    
    public function getShortUrlAttribute()
    {
        return url('/s/' . $this->short_code);
    }

    public function getStatusBadgeAttribute()
    {
        if (!$this->is_active) {
            return '<span class="badge bg-danger">Inactive</span>';
        }
        
        if ($this->expires_at && $this->expires_at->isPast()) {
            return '<span class="badge bg-warning">Expired</span>';
        }
        
        return '<span class="badge bg-success">Active</span>';
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isActive()
    {
        return $this->is_active && !$this->isExpired();
    }

    public static function generateShortCode($length = 6)
    {
        do {
            $code = Str::random($length);
        } while (self::where('short_code', $code)->exists());
        
        return $code;
    }

    public function incrementClicks()
    {
        $this->increment('clicks');
        return $this;
    }
}