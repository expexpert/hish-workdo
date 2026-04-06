<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WhatsAppDocumentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('app.name'))
            ->subject('New Document Received: ' . $this->details['type'] . ' from ' . $this->details['customer_name'])
            ->view('email.whatsapp_document_notification')
            ->with(['data' => $this->details]);
    }
}
