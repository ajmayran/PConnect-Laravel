<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;

class MailServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Mail::beforeSending(function (Message $message) {
            // Add logo to message headers
            $logoUrl = config('mail.logo');
            if ($logoUrl && $message instanceof Email) {
                $message->getHeaders()->addTextHeader('X-Entity-Ref-ID', 'PConnect');
                $message->getHeaders()->addTextHeader('X-Entity-ID', 'PConnect');
                $message->getHeaders()->addTextHeader('X-Logo', $logoUrl);

                $message->getHeaders()->addTextHeader('X-Entity-Ref-ID', 'PConnect');
                $message->getHeaders()->addTextHeader('X-Entity-ID', 'PConnect');
                $message->getHeaders()->addTextHeader('X-Logo', $logoUrl);

                // Add organization header
                $message->getHeaders()->addTextHeader('Organization', 'PConnect');
            }
        });
    }
}
