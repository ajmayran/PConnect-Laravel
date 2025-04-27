<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DistributorApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        // Get the subject from the config
        $subject = config('mail.distributor_approval.subject');
        
        // Track opens and clicks if configured
        $trackOpens = config('mail.distributor_approval.track_opens', false);
        $trackClicks = config('mail.distributor_approval.track_clicks', false);
        
        $mail = $this->subject($subject)
            ->view('mails.distributor-confirm')
            ->with(['user' => $this->user]);
            
        // Add email tracking if enabled
        if ($trackOpens) {
            $mail->withSymfonyMessage(function ($message) {
                $message->getHeaders()->addTextHeader('X-Track-Opens', 'true');
            });
        }
        
        if ($trackClicks) {
            $mail->withSymfonyMessage(function ($message) {
                $message->getHeaders()->addTextHeader('X-Track-Clicks', 'true');
            });
        }
        
        return $mail;
    }
}