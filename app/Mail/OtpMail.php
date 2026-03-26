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
     * The user instance (if exists) or array with name.
     *
     * @var mixed
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
     * @param  \App\Models\User|string|array  $userOrName - Can be User object, name string, or array with 'name' key
     * @param  string  $otp
     * @return void
     */
    public function __construct($userOrName, string $otp)
    {
        // Handle different input types
        if (is_string($userOrName)) {
            // Just a name string (for pending users)
            $this->user = ['name' => $userOrName];
        } elseif (is_array($userOrName) && isset($userOrName['name'])) {
            // Array with name
            $this->user = $userOrName;
        } else {
            // User object
            $this->user = $userOrName;
        }

        $this->otp = $otp;

        Log::info('OtpMail job created', [
            'user_id' => (is_object($this->user) && method_exists($this->user, 'id')) ? $this->user->id : null,
            'email' => (is_object($this->user) && method_exists($this->user, 'email')) ? $this->user->email : null,
            'name' => is_array($this->user) ? ($this->user['name'] ?? 'unknown') : (is_object($this->user) && method_exists($this->user, 'name') ? $this->user->name : 'unknown'),
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
        // Handle different user formats for logging
        $userId = null;
        $userName = null;

        if (is_object($this->user)) {
            $userId = $this->user->id ?? null;
            $userName = $this->user->name ?? null;
        } elseif (is_array($this->user)) {
            $userName = $this->user['name'] ?? null;
        }

        Log::info('Rendering OTP email content', [
            'view' => 'emails.otp-simple',
            'user_id' => $userId,
            'user_name' => $userName,
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

    /**
     * Get user name from different user formats
     *
     * @return string
     */
    public function getUserName(): string
    {
        if (is_object($this->user) && method_exists($this->user, 'name')) {
            return $this->user->name;
        } elseif (is_array($this->user) && isset($this->user['name'])) {
            return $this->user['name'];
        }
        return 'User';
    }
}
