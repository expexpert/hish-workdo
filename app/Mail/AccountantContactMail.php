<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountantContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $attachment;

    public function __construct($details, $attachment = null)
    {
        $this->details = $details; // Array containing customer info, subject, and message
        $this->attachment = $attachment;
    }

    public function build()
    {
        $email = $this->from($this->details['from_email'], $this->details['customer_name'])
            ->subject('New Customer Document: ' . $this->details['subject'])
            ->view('email.accountant_contact') // We will create this view
            ->with(['data' => $this->details]);

        // Handle attachment
        if ($this->attachment) {
            $email->attach($this->attachment->getRealPath(), [
                'as'   => $this->attachment->getClientOriginalName(),
                'mime' => $this->attachment->getMimeType(),
            ]);
        }

        return $email;
    }
}