@extends('layouts.app')

@section('title', 'Create Short URL')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Create Short URL</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('short-urls.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title (Optional)</label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               placeholder="e.g., Product Launch Page">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Give your URL a descriptive name for easy identification.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="original_url" class="form-label">
                            Original URL <span class="text-danger">*</span>
                        </label>
                        <input type="url" 
                               class="form-control @error('original_url') is-invalid @enderror" 
                               id="original_url" 
                               name="original_url" 
                               value="{{ old('original_url') }}"
                               placeholder="https://example.com/very-long-url-path"
                               required>
                        @error('original_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Enter the full URL you want to shorten.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                        <input type="datetime-local" 
                               class="form-control @error('expires_at') is-invalid @enderror" 
                               id="expires_at" 
                               name="expires_at" 
                               value="{{ old('expires_at') }}">
                        @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Leave empty for no expiration.</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>Note:</strong> The short code will be automatically generated after submission.
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('short-urls.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Create Short URL
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Tips for Better URLs</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i> Use descriptive titles for easy management</li>
                    <li><i class="fas fa-check text-success me-2"></i> Set expiration dates for temporary campaigns</li>
                    <li><i class="fas fa-check text-success me-2"></i> Track performance through click analytics</li>
                    <li><i class="fas fa-check text-success me-2"></i> Deactivate URLs when no longer needed</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Set minimum datetime for expiration (current time)
    document.getElementById('expires_at').min = new Date().toISOString().slice(0, 16);
    
    // Add Font Awesome icons
    document.head.insertAdjacentHTML('beforeend', '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">');
</script>
@endpush
@endsection