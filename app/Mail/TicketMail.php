<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $qrDataUri;

    public function __construct(Ticket $ticket, $qrDataUri)
    {
        $this->ticket = $ticket;
        $this->qrDataUri = $qrDataUri;
    }

    public function build()
    {
        return $this->subject('Your Event Ticket')
            ->view('emails.ticket')
            ->with([
                'ticket' => $this->ticket,
                'qrDataUri' => $this->qrDataUri,
            ]);
    }
}
