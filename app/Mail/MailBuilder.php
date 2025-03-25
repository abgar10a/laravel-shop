<?php

namespace App\Mail;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailBuilder extends Mailable
{
    use Queueable;

    use Queueable, SerializesModels;

    public $email;
    public $template;

    public function __construct(Email $email, $template)
    {
        $this->email = $email;
        $this->template = $template;
    }

    public function build()
    {
        return $this->subject($this->email->subject)
            ->view("emails.{$this->template}")
            ->with(['data' => $this->email->data, 'user' => $this->email->user]);
    }
}
