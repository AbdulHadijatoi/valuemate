<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function build() {
        return $this->subject('Your Valuation Request Status Updated')
                    ->view('emails.status_updated')
                    ->with(['data' => $this->data, 'title' => 'Status Update']);
    }
}
