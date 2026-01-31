<?php
// routes/web.php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Short URLs
    Route::resource('short-urls', ShortUrlController::class)->except(['show']);
    Route::patch('/short-urls/{id}/toggle-status', [ShortUrlController::class, 'toggleStatus'])->name('short-urls.toggle-status');
    
    // Invitations
    Route::get('/invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::delete('/invitations/{id}', [InvitationController::class, 'cancel'])->name('invitations.cancel');
});

// Short URL Redirect (requires auth check)
Route::get('/s/{shortCode}', [ShortUrlController::class, 'redirect'])
    ->name('short-urls.redirect');

// Invitation Acceptance
Route::get('/invitation/accept/{token}', [InvitationController::class, 'accept'])
    ->name('invitation.accept');
Route::post('/invitation/complete/{token}', [InvitationController::class, 'complete'])
    ->name('invitation.complete');

// Authentication routes from Breeze
require __DIR__.'/auth.php';