<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommonEmailTemplate extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $settings;
    public $attachment;

    public function __construct($template, $settings, $attachment = null)
    {
        $this->template = $template;
        $this->settings = $settings;
        $this->attachment = $attachment;
    }

    public function build()
    {
        $email = $this->from($this->settings['mail_from_address'], $this->template->from)
            ->markdown('email.common_email_template')
            ->subject($this->template->subject)
            ->with('content', $this->template->content);

        // Logic to handle the attachment if it exists
        if ($this->attachment) {
            $email->attach($this->attachment->getRealPath(), [
                'as'   => $this->attachment->getClientOriginalName(),
                'mime' => $this->attachment->getMimeType(),
            ]);
        }

        return $email;
    }}
