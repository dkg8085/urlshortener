@extends('layouts.app')

@section('title', 'Invitations')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>User Invitations</h2>
            <a href="{{ route('invitations.create') }}" class="btn btn-primary">Invite User</a>
        </div>
        
        @if($invitations->isEmpty())
            <div class="alert alert-info">
                No pending invitations found.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Company</th>
                            <th>Invited On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invitations as $invitation)
                            <tr>
                                <td>{{ $invitation->name }}</td>
                                <td>{{ $invitation->email }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $invitation->getRoleNames()->first() }}
                                    </span>
                                </td>
                                <td>{{ $invitation->company->name ?? 'N/A' }}</td>
                                <td>{{ $invitation->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge bg-warning">Pending</span>
                                </td>
                                <td>
                                    <form method="POST" 
                                          action="{{ route('invitations.cancel', $invitation->id) }}" 
                                          onsubmit="return confirm('Are you sure you want to cancel this invitation?');"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Cancel
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Invitation Rules</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @if(auth()->user()->isSuperAdmin())
                    <div class="col-md-6">
                        <h6>SuperAdmin Permissions</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success me-2"></i> Can invite: Member, Sales, Manager</li>
                            <li><i class="fas fa-times-circle text-danger me-2"></i> Cannot invite: Admin</li>
                        </ul>
                    </div>
                    @endif
                    
                    @if(auth()->user()->isAdmin())
                    <div class="col-md-6">
                        <h6>Admin Permissions</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success me-2"></i> Can invite: Sales, Manager</li>
                            <li><i class="fas fa-times-circle text-danger me-2"></i> Cannot invite: Admin, Member</li>
                        </ul>
                    </div>
                    @endif
                    
                    <div class="col-md-6">
                        <h6>General Information</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-envelope me-2"></i> Invitations are sent via email</li>
                            <li><i class="fas fa-clock me-2"></i> Invitations expire in 7 days</li>
                            <li><i class="fas fa-user-plus me-2"></i> Users must accept invitation to join</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add Font Awesome icons
    document.head.insertAdjacentHTML('beforeend', '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">');
</script>
@endpush
@endsection