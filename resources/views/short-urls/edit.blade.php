@extends('layouts.app')

@section('title', 'Edit Short URL')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Short URL</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('short-urls.update', $shortUrl->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title (Optional)</label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $shortUrl->title) }}"
                               placeholder="e.g., Product Launch Page">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="original_url" class="form-label">
                            Original URL <span class="text-danger">*</span>
                        </label>
                        <input type="url" 
                               class="form-control @error('original_url') is-invalid @enderror" 
                               id="original_url" 
                               name="original_url" 
                               value="{{ old('original_url', $shortUrl->original_url) }}"
                               required>
                        @error('original_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                        <input type="datetime-local" 
                               class="form-control @error('expires_at') is-invalid @enderror" 
                               id="expires_at" 
                               name="expires_at" 
                               value="{{ old('expires_at', $shortUrl->expires_at ? $shortUrl->expires_at->format('Y-m-d\TH:i') : '') }}">
                        @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Short Code</label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ $shortUrl->short_code }}"
                               readonly>
                        <div class="form-text">Short code cannot be changed.</div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('short-urls.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Update Short URL
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">URL Information</h6>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Created By:</dt>
                    <dd class="col-sm-9">{{ $shortUrl->user->name }}</dd>
                    
                    <dt class="col-sm-3">Company:</dt>
                    <dd class="col-sm-9">{{ $shortUrl->company->name }}</dd>
                    
                    <dt class="col-sm-3">Total Clicks:</dt>
                    <dd class="col-sm-9">{{ $shortUrl->clicks }}</dd>
                    
                    <dt class="col-sm-3">Status:</dt>
                    <dd class="col-sm-9">{!! $shortUrl->status_badge !!}</dd>
                    
                    <dt class="col-sm-3">Short URL:</dt>
                    <dd class="col-sm-9">
                        <code>{{ url('/s/' . $shortUrl->short_code) }}</code>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Set minimum datetime for expiration (current time)
    document.getElementById('expires_at').min = new Date().toISOString().slice(0, 16);
</script>
@endpush
@endsection