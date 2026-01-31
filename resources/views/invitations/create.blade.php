@extends('layouts.app')

@section('title', 'Invite User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Invite New User</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('invitations.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" 
                                name="role" 
                                required>
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" 
                                        {{ old('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            @if(auth()->user()->isSuperAdmin())
                                You can invite: Member, Sales, Manager
                            @elseif(auth()->user()->isAdmin())
                                You can invite: Sales, Manager
                            @endif
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <strong>Important:</strong> The invited user will receive an email with instructions to set up their account. 
                        They must accept the invitation within 7 days.
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('invitations.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Send Invitation
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Role Descriptions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if(auth()->user()->isSuperAdmin())
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-primary">Member</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-eye me-2"></i> View URLs (not own)</li>
                                <li><i class="fas fa-times text-danger me-2"></i> Cannot create URLs</li>
                                <li><i class="fas fa-times text-danger me-2"></i> Cannot invite users</li>
                            </ul>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-success">Sales</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-plus me-2"></i> Create short URLs</li>
                                <li><i class="fas fa-eye me-2"></i> View company URLs</li>
                                <li><i class="fas fa-times text-danger me-2"></i> Cannot invite users</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-info">Manager</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-plus me-2"></i> Create short URLs</li>
                                <li><i class="fas fa-edit me-2"></i> Edit/Delete URLs</li>
                                <li><i class="fas fa-eye me-2"></i> View company URLs</li>
                                <li><i class="fas fa-times text-danger me-2"></i> Cannot invite users</li>
                            </ul>
                        </div>
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