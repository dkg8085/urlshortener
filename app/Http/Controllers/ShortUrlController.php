<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShortUrlRequest;
use App\Services\ShortUrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShortUrlController extends Controller
{
    protected $shortUrlService;

    public function __construct(ShortUrlService $shortUrlService)
    {
        $this->middleware('auth');
        $this->shortUrlService = $shortUrlService;
    }

    public function index()
    {
        $user = Auth::user();
        $shortUrls = $this->shortUrlService->getUrlsForUser($user);
        
        return view('short-urls.index', compact('shortUrls'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user->canCreateUrls()) {
            abort(403, 'You are not authorized to create short URLs.');
        }
        
        return view('short-urls.create');
    }

    public function store(ShortUrlRequest $request)
    {
        try {
            $user = Auth::user();
            $shortUrl = $this->shortUrlService->createShortUrl($request->validated(), $user);
            
            return redirect()->route('short-urls.index')
                ->with('success', 'Short URL created successfully! Your code: ' . $shortUrl->short_code);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $shortUrl = \App\Models\ShortUrl::findOrFail($id);
        
        
        if ($shortUrl->user_id !== $user->id && !$user->isManager()) {
            abort(403, 'You can only edit your own URLs.');
        }
        
        return view('short-urls.edit', compact('shortUrl'));
    }

    public function update(ShortUrlRequest $request, $id)
    {
        try {
            $user = Auth::user();
            $shortUrl = $this->shortUrlService->updateShortUrl($id, $request->validated(), $user);
            
            return redirect()->route('short-urls.index')
                ->with('success', 'Short URL updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $user = Auth::user();
            $shortUrl = $this->shortUrlService->toggleStatus($id, $user);
            
            $status = $shortUrl->is_active ? 'activated' : 'deactivated';
            return redirect()->back()
                ->with('success', "Short URL {$status} successfully!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $this->shortUrlService->deleteShortUrl($id, $user);
            
            return redirect()->route('short-urls.index')
                ->with('success', 'Short URL deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function redirect($shortCode)
    {
        $user = Auth::user();
        $redirectUrl = $this->shortUrlService->redirectShortUrl($shortCode, $user);
        
        return redirect()->away($redirectUrl);
    }
}