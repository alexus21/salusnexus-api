<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConfirmAppointmentMail extends Mailable {
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     */
    public function __construct($details) {
        $this->details = $details;
    }

    /**
     * Build the message.
     */
    public function build(): ConfirmAppointmentMail {
        Log::info("Email enviado a: " . $this->details['email'] . " con el asunto: " . $this->details['subject']);

        return $this->subject($this->details['subject'])
            ->view('appointment-confirmation');
    }
}
