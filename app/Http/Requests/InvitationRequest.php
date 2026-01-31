<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->canInviteUsers();
    }

    public function rules(): array
    {
        $user = Auth::user();
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ];
        
        if ($user->isSuperAdmin()) {
            $rules['role'] = ['required', Rule::in(['member', 'sales', 'manager'])];
        } elseif ($user->isAdmin()) {
            $rules['role'] = ['required', Rule::in(['sales', 'manager'])];
        }
        
        return $rules;
    }

    public function messages(): array
    {
        return [
            'role.in' => 'You are not authorized to invite users with this role.',
            'email.unique' => 'A user with this email already exists.',
        ];
    }
}