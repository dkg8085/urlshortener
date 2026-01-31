<?php

namespace App\Services;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShortUrlService
{
    public function createShortUrl(array $data, User $user)
    {
        
        if (!$user->canCreateUrls()) {
            throw new \Exception('You are not authorized to create short URLs.');
        }

        return DB::transaction(function () use ($data, $user) {
            return ShortUrl::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'title' => $data['title'] ?? null,
                'original_url' => $data['original_url'],
                'short_code' => ShortUrl::generateShortCode(),
                'expires_at' => $data['expires_at'] ?? null,
                'is_active' => true
            ]);
        });
    }

    public function updateShortUrl($id, array $data, User $user)
    {
        $shortUrl = ShortUrl::findOrFail($id);

       
        if ($shortUrl->user_id !== $user->id && !$user->isManager()) {
            throw new \Exception('You can only update your own URLs.');
        }

        $shortUrl->update($data);
        return $shortUrl;
    }

    public function deleteShortUrl($id, User $user)
    {
        $shortUrl = ShortUrl::findOrFail($id);

       
        if ($shortUrl->user_id !== $user->id && !$user->isManager()) {
            throw new \Exception('You can only delete your own URLs.');
        }

        return $shortUrl->delete();
    }

    public function toggleStatus($id, User $user)
    {
        $shortUrl = ShortUrl::findOrFail($id);

        
        if ($shortUrl->user_id !== $user->id && !$user->isManager() && !$user->isAdmin()) {
            throw new \Exception('You are not authorized to update this URL.');
        }

        $shortUrl->is_active = !$shortUrl->is_active;
        $shortUrl->save();
        
        return $shortUrl;
    }

    public function redirectShortUrl($shortCode, User $user = null)
    {
        
        $shortUrl = ShortUrl::where('short_code', $shortCode)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$shortUrl) {
            abort(404, 'Short URL not found or expired.');
        }

        
        if (!$user) {
            return redirect()->route('login');
        }

        
        if ($user->isSuperAdmin()) {
            abort(403, 'SuperAdmin cannot access short URLs.');
        }

        if ($user->isAdmin() && $shortUrl->company_id === $user->company_id) {
            abort(403, 'Admin cannot access URLs from their own company.');
        }

        if ($user->isMember() && $shortUrl->user_id === $user->id) {
            abort(403, 'Member cannot access URLs created by themselves.');
        }

        
        $shortUrl->incrementClicks();

        return $shortUrl->original_url;
    }

    public function getUrlsForUser(User $user)
    {
        return $user->getVisibleUrls();
    }

public function getStatsForUser(User $user)
    {
        $stats = [];
        
        if ($user->isSuperAdmin()) {
            $stats = [
                'total_companies' => \App\Models\Company::count(),
                'total_users' => \App\Models\User::count(),
                'active_urls' => ShortUrl::where('is_active', true)->count(),
                'total_clicks' => ShortUrl::sum('clicks')
            ];
        } elseif ($user->isAdmin()) {
            $stats = [
                'company_users' => $user->company ? $user->company->users()->count() : 0,
                'pending_invitations' => $user->company ? $user->company->users()->whereNotNull('invitation_token')->count() : 0,
                'external_urls' => ShortUrl::where('company_id', '!=', $user->company_id)->count(),
                'total_clicks_external' => ShortUrl::where('company_id', '!=', $user->company_id)->sum('clicks')
            ];
        } else {
            $stats = [
                'my_urls' => $user->shortUrls()->count(),
                'my_clicks' => $user->shortUrls()->sum('clicks'),
                'active_urls' => $user->shortUrls()->where('is_active', true)->count(),
                'expired_urls' => $user->shortUrls()->where('expires_at', '<', now())->count()
            ];
        }
        
        return $stats;
    }
}