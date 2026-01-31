<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ShortUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->canCreateUrls();
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'original_url' => 'required|url|max:1000',
            'expires_at' => 'nullable|date|after:now',
        ];
    }

    public function messages(): array
    {
        return [
            'original_url.required' => 'The URL field is required.',
            'original_url.url' => 'Please enter a valid URL.',
            'expires_at.after' => 'Expiration date must be in the future.',
        ];
    }
}