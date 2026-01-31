{{-- resources/views/emails/user-invitation.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Invitation to Join URL Shortener</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            padding: 30px;
            background: #f8f9fc;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #4e73df;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>You're Invited!</h1>
    </div>
    
    <div class="content">
        <p>Hello {{ $user->name }},</p>
        
        <p>You have been invited by <strong>{{ $inviter->name }}</strong> to join the URL Shortener platform.</p>
        
        <p><strong>Your Role:</strong> {{ ucfirst($user->getRoleNames()->first()) }}</p>
        
        <p>To accept this invitation and set up your account, please click the button below:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $invitationLink }}" class="button">
                Accept Invitation
            </a>
        </div>
        
        <p>This invitation link will expire in 7 days.</p>
        
        <p>If you did not expect this invitation, please ignore this email.</p>
        
        <p>Best regards,<br>
        <strong>URL Shortener Team</strong></p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} URL Shortener. All rights reserved.</p>
        <p>This is an automated message, please do not reply to this email.</p>
    </div>
</body>
</html>