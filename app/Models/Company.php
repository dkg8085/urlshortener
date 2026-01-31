<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'is_active'];

   
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function shortUrls()
    {
        return $this->hasMany(ShortUrl::class);
    }

    public function activeUsers()
    {
        return $this->users()->where('status', 'active');
    }

    public function pendingInvitations()
    {
        return $this->users()->whereNotNull('invitation_token');
    }

    
    public function activeUrls()
    {
        return $this->shortUrls()->where('is_active', true);
    }
}