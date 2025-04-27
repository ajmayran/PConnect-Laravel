<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyDistributorEmail extends BaseVerifyEmail
{
    /**
     * @var mixed
     */
    protected $notifiable;

    /**
     * Create a new notification instance.
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function __construct($notifiable = null)
    {
        $this->notifiable = $notifiable;
    }

    /**
     * Get the verification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        $mailMessage = (new MailMessage)
            ->subject('Verify Your Distributor Account Email Address')
            ->view('mails.distributor-verify-email', [
                'user' => $this->notifiable,
                'verificationUrl' => $url
            ]);
        
        $mailMessage->withSymfonyMessage(function ($message) {
            // Make sure the logo config value exists before adding it as a header
            if (config('mail.logo')) {
                $message->getHeaders()->addTextHeader('X-Logo', config('mail.logo'));
            }
            
            $message->getHeaders()->addTextHeader('X-Entity-Ref-ID', 'PConnect');
            $message->getHeaders()->addTextHeader('Organization', 'PConnect');
        });
        
        return $mailMessage;
    }
}