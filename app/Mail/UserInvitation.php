<?php

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $invitationLink;
    public $inviter;

    public function __construct(User $user, $invitationLink, User $inviter)
    {
        $this->user = $user;
        $this->invitationLink = $invitationLink;
        $this->inviter = $inviter;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation to Join URL Shortener',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-invitation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}