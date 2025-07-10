<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $recipientType;

    public function __construct($data, $recipientType = 'user') {
        $this->data = $data;
        $this->recipientType = $recipientType; // 'user', 'company', or 'admin'
    }

    public function build() {
        return $this->subject('Valuation Payment Successful')
                    ->view('emails.payment_success')
                    ->with([
                        'data' => $this->data,
                        'recipientType' => $this->recipientType,
                        'title' => 'Payment Successful'
                    ]);
    }
}
