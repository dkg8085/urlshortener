<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserInvitation;

class InvitationService
{
    public function inviteUser(array $data, User $inviter)
    {
        
        if (!$inviter->canInviteUsers()) {
            throw new \Exception('You are not authorized to invite users.');
        }

        
        $this->validateRolePermission($data['role'], $inviter);

       
        if (User::where('email', $data['email'])->exists()) {
            throw new \Exception('A user with this email already exists.');
        }

        $token = Str::random(60);
        
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(16)),
            'company_id' => $inviter->company_id,
            'invitation_token' => $token,
            'status' => 'inactive'
        ]);

        $user->assignRole($data['role']);

        // Send invitation email
        $this->sendInvitationEmail($user, $token, $inviter);

        return $user;
    }

    public function acceptInvitation($token, array $data)
    {
        $user = User::where('invitation_token', $token)->first();

        if (!$user) {
            throw new \Exception('Invalid or expired invitation token.');
        }

        $user->update([
            'password' => Hash::make($data['password']),
            'invitation_token' => null,
            'invitation_accepted_at' => now(),
            'status' => 'active'
        ]);

        return $user;
    }

    public function cancelInvitation($id, User $user)
    {
        $invitedUser = User::findOrFail($id);

        // Check authorization
        if ($invitedUser->company_id !== $user->company_id) {
            throw new \Exception('You can only cancel invitations from your company.');
        }

        if (!$invitedUser->isInvited()) {
            throw new \Exception('This invitation is already accepted or cancelled.');
        }

        return $invitedUser->delete();
    }

    public function getPendingInvitations(User $user)
    {
        if ($user->isSuperAdmin()) {
            return User::whereNotNull('invitation_token')->get();
        }
        
        return $user->company->pendingInvitations()->get();
    }

    public function getAvailableRoles(User $user)
    {
        if ($user->isSuperAdmin()) {
            return ['member', 'sales', 'manager'];
        }
        
        if ($user->isAdmin()) {
            return ['sales', 'manager'];
        }
        
        return [];
    }

    private function validateRolePermission($role, User $inviter)
    {
        if ($inviter->isSuperAdmin()) {
            // SuperAdmin cannot invite Admin
            if ($role === 'admin') {
                throw new \Exception('SuperAdmin cannot invite Admin users.');
            }
            return;
        }

        if ($inviter->isAdmin()) {
            // Admin cannot invite Admin or Member
            if (in_array($role, ['admin', 'member'])) {
                throw new \Exception('Admin cannot invite Admin or Member users.');
            }
            return;
        }

        throw new \Exception('You are not authorized to invite users.');
    }

    private function sendInvitationEmail(User $user, $token, User $inviter)
    {
        $invitationLink = route('invitation.accept', $token);
        
        Mail::to($user->email)->send(new UserInvitation($user, $invitationLink, $inviter));
    }
}