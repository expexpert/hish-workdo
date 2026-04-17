<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WhatsAppDocumentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $userId;

    public function __construct($details, $userId = 1)
    {
        $this->details = $details;
        $this->userId = $userId;
    }

    public function build()
    {
        \App\Models\Utility::getSMTPDetails($this->userId);

        return $this->from(config('mail.from.address'), config('app.name'))
            ->subject('New Document Received: ' . $this->details['type'] . ' from ' . $this->details['customer_name'])
            ->view('email.whatsapp_document_notification')
            ->with(['data' => $this->details]);
    }
}
