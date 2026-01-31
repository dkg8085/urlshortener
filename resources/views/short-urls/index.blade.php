@extends('layouts.app')

@section('title', 'Short URLs')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Short URLs</h2>
            @if(auth()->user()->canCreateUrls())
                <a href="{{ route('short-urls.create') }}" class="btn btn-primary">Create New URL</a>
            @endif
        </div>
        
        @if($shortUrls->isEmpty())
            <div class="alert alert-info">
                @if(auth()->user()->isSuperAdmin())
                    <strong>SuperAdmin View:</strong> You cannot view short URLs.
                @elseif(auth()->user()->isAdmin())
                    <strong>Admin View:</strong> No URLs found from other companies.
                @elseif(auth()->user()->isMember())
                    <strong>Member View:</strong> No URLs found from other users.
                @else
                    <strong>Sales/Manager View:</strong> You haven't created any short URLs yet.
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Short Code</th>
                            <th>Original URL</th>
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <th>Created By</th>
                                <th>Company</th>
                            @endif
                            <th>Clicks</th>
                            <th>Status</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shortUrls as $url)
                            <tr>
                                <td>{{ $url->title ?? 'Untitled' }}</td>
                                <td>
                                    <code>{{ $url->short_code }}</code><br>
                                    <small class="text-muted">{{ url('/s/' . $url->short_code) }}</small>
                                </td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                    <a href="{{ $url->original_url }}" target="_blank">{{ $url->original_url }}</a>
                                </td>
                                @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                    <td>{{ $url->user->name }}</td>
                                    <td>{{ $url->company->name }}</td>
                                @endif
                                <td>
                                    <span class="badge bg-primary rounded-pill">{{ $url->clicks }}</span>
                                </td>
                                <td>{!! $url->status_badge !!}</td>
                                <td>
                                    @if($url->expires_at)
                                        {{ $url->expires_at->format('Y-m-d') }}<br>
                                        <small class="text-muted">{{ $url->expires_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    @if(auth()->user()->canCreateUrls() && ($url->user_id == auth()->id() || auth()->user()->isManager()))
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('short-urls.edit', $url->id) }}" class="btn btn-outline-info" title="Edit">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('short-urls.toggle-status', $url->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-warning" title="{{ $url->is_active ? 'Deactivate' : 'Activate' }}">
                                                    {{ $url->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('short-urls.destroy', $url->id) }}" onsubmit="return confirm('Are you sure you want to delete this URL?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <a href="{{ route('short-urls.redirect', $url->short_code) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            Visit
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <small class="text-muted">
                    Showing {{ $shortUrls->count() }} URL(s) based on your role permissions
                </small>
            </div>
        @endif
    </div>
</div>

<script>
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endsection