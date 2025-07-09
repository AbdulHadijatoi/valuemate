<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function build() {
        return $this->subject('Your Payment Was Successful')
                    ->view('emails.payment_success')
                    ->with(['data' => $this->data]);
    }
}
