<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The OTP code.
     *
     * @var string
     */
    public $otp;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @param  string  $otp
     * @return void
     */
    public function __construct($user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;

        Log::info('OtpMail job created', [
            'user_id' => $user->id ?? null,
            'email' => $user->email ?? 'unknown',
            'otp' => $otp,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Get message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Verification Code',
        );
    }

    /**
     * Get message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        Log::info('Rendering OTP email content', [
            'view' => 'emails.otp-simple',
            'user_id' => $this->user->id ?? null,
            'user_name' => $this->user->name ?? null,
            'otp' => $this->otp,
            'appName' => config('app.name'),
        ]);

        return new Content(
            view: 'emails.otp-simple',
            with: [
                'user' => $this->user,
                'otp' => $this->otp,
                'appName' => config('app.name'),
            ],
        );
    }

    /**
     * Get attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
