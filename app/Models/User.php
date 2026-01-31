<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'invitation_token',
        'invitation_accepted_at',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'invitation_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'invitation_accepted_at' => 'datetime'
    ];

    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function shortUrls()
    {
        return $this->hasMany(ShortUrl::class);
    }

  
    public function isSuperAdmin()
    {
        return $this->hasRole('superadmin');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isMember()
    {
        return $this->hasRole('member');
    }

    public function isSales()
    {
        return $this->hasRole('sales');
    }

    public function isManager()
    {
        return $this->hasRole('manager');
    }

   
    public function canCreateUrls()
    {
        return $this->isSales() || $this->isManager();
    }

    
    public function canInviteUsers()
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }


    public function canViewUrls()
    {
        return !$this->isSuperAdmin();
    }

    public function getVisibleUrls()
    {
        if ($this->isSuperAdmin()) {
            return collect(); 
        }

        if ($this->isAdmin()) {
            return ShortUrl::where('company_id', '!=', $this->company_id)
                ->with(['user', 'company'])
                ->get();
        }

        if ($this->isMember()) {
            return ShortUrl::where('user_id', '!=', $this->id)
                ->with(['user', 'company'])
                ->get();
        }

        
        return $this->shortUrls()->with('company')->get();
    }

  
    public function isInvited()
    {
        return !is_null($this->invitation_token);
    }

    public function hasAcceptedInvitation()
    {
        return !is_null($this->invitation_accepted_at);
    }

    
    public function getRoleName()
    {
        return $this->getRoleNames()->first();
    }

    
    public function getCompanyName()
    {
        return $this->company ? $this->company->name : 'No Company';
    }
}