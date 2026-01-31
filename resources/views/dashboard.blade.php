@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <h2>Dashboard</h2>
        <p class="text-muted">Welcome back, {{ auth()->user()->name }}!</p>
        
        <div class="row">
            @if(auth()->user()->isSuperAdmin())
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Companies</h5>
                            <p class="card-text display-6">{{ $stats['total_companies'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text display-6">{{ $stats['total_users'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Active URLs</h5>
                            <p class="card-text display-6">{{ $stats['active_urls'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Clicks</h5>
                            <p class="card-text display-6">{{ $stats['total_clicks'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            @elseif(auth()->user()->isAdmin())
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h5 class="card-title">Company Users</h5>
                            <p class="card-text display-6">{{ $stats['company_users'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body">
                            <h5 class="card-title">Pending Invitations</h5>
                            <p class="card-text display-6">{{ $stats['pending_invitations'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body">
                            <h5 class="card-title">External URLs</h5>
                            <p class="card-text display-6">{{ $stats['external_urls'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body">
                            <h5 class="card-title">External Clicks</h5>
                            <p class="card-text display-6">{{ $stats['total_clicks_external'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h5 class="card-title">My URLs</h5>
                            <p class="card-text display-6">{{ $stats['my_urls'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body">
                            <h5 class="card-title">Total Clicks</h5>
                            <p class="card-text display-6">{{ $stats['my_clicks'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body">
                            <h5 class="card-title">Active URLs</h5>
                            <p class="card-text display-6">{{ $stats['active_urls'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-danger">
                        <div class="card-body">
                            <h5 class="card-title">Expired URLs</h5>
                            <p class="card-text display-6">{{ $stats['expired_urls'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    @if(auth()->user()->canCreateUrls())
                        <a href="{{ route('short-urls.create') }}" class="btn btn-primary me-2">Create Short URL</a>
                    @endif
                    @if(auth()->user()->canInviteUsers())
                        <a href="{{ route('invitations.create') }}" class="btn btn-success me-2">Invite User</a>
                    @endif
                    <a href="{{ route('short-urls.index') }}" class="btn btn-info">View URLs</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection