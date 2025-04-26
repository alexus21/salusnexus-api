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
        return $this->subject($this->details['subject'])
            ->view('appointment-confirmation');
    }
}
