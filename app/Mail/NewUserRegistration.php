<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserRegistration extends Mailable
{
    use Queueable, SerializesModels;

    public User   $user;
    public string $approvalUrl;
    public string $rejectUrl;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->approvalUrl = route('admin.users.approve', [
            'user' => $user->id,
            'token' => encrypt($user->id . '|' . now()->timestamp)
        ]);
        $this->rejectUrl = route('admin.users.reject', [
            'user' => $user->id,
            'token' => encrypt($user->id . '|' . now()->timestamp)
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova Solicitação de Registro - ' . $this->user->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-user-registration',
        );
    }
}
