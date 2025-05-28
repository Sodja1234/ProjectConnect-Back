<?php

namespace App\Listeners;

use App\Mail\RegisteredUserMail;
use App\Events\RegisteredUserEvent;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisteredUserListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(private Mailer $mailer)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(RegisteredUserEvent $event): void
    {
        $this->mailer->send(new RegisteredUserMail($event->user));
    }
}