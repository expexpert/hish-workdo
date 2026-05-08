<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $accountant;
    public $customer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url, $accountant, $customer)
    {
        $this->url = $url;
        $this->accountant = $accountant;
        $this->customer = $customer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.customer_invitation')
            ->subject('Invitation to join ' . $this->accountant->name);
    }
}
