<?php

namespace App\Http\Controllers;

use App\Services\ShortUrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
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
        $stats = $this->shortUrlService->getStatsForUser($user);
        
        return view('dashboard', compact('stats'));
    }
}