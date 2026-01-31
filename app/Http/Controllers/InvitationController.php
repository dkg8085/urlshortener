<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationRequest;
use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    protected $invitationService;

    public function __construct(InvitationService $invitationService)
    {
        $this->middleware('auth');
        $this->invitationService = $invitationService;
    }

    public function index()
    {
        $user = Auth::user();
        
        if (!$user->canInviteUsers()) {
            abort(403, 'Unauthorized action.');
        }
        
        $invitations = $this->invitationService->getPendingInvitations($user);
        
        return view('invitations.index', compact('invitations'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user->canInviteUsers()) {
            abort(403, 'Unauthorized action.');
        }
        
        $roles = $this->invitationService->getAvailableRoles($user);
        
        return view('invitations.create', compact('roles'));
    }

    public function store(InvitationRequest $request)
    {
        try {
            $user = Auth::user();
            $invitedUser = $this->invitationService->inviteUser($request->validated(), $user);
            
            return redirect()->route('invitations.index')
                ->with('success', "Invitation sent to {$invitedUser->email} successfully!");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function cancel($id)
    {
        try {
            $user = Auth::user();
            $this->invitationService->cancelInvitation($id, $user);
            
            return redirect()->route('invitations.index')
                ->with('success', 'Invitation cancelled successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function accept($token)
    {
        $user = \App\Models\User::where('invitation_token', $token)->first();
        
        if (!$user) {
            abort(404, 'Invitation not found or already accepted.');
        }
        
        return view('auth.invitation-accept', compact('token', 'user'));
    }

    public function complete(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);
        
        try {
            $user = $this->invitationService->acceptInvitation($token, $request->all());
            Auth::login($user);
            
            return redirect()->route('dashboard')
                ->with('success', 'Account setup completed successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}