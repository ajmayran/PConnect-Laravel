<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Mime\Email;

class MailServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Use Event facade to listen for MessageSending event
        Event::listen(MessageSending::class, function (MessageSending $event) {
            $message = $event->message;
            
            // Add logo to message headers
            $logoUrl = config('mail.logo');
            if ($logoUrl && $message instanceof Email) {
                $message->getHeaders()->addTextHeader('X-Entity-Ref-ID', 'PConnect');
                $message->getHeaders()->addTextHeader('X-Entity-ID', 'PConnect');
                $message->getHeaders()->addTextHeader('X-Logo', $logoUrl);
                
                // Add organization header
                $message->getHeaders()->addTextHeader('Organization', 'PConnect');
            }
        });
    }
}