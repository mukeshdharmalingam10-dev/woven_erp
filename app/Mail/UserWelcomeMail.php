<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $loginUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $password, $loginUrl)
    {
        $this->user = $user;
        $this->password = $password;
        $this->loginUrl = $loginUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to ' . config('app.name'))
            ->view('emails.user-welcome')
            ->with([
                'user' => $this->user,
                'password' => $this->password,
                'loginUrl' => $this->loginUrl,
            ]);
    }
}
