<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $filePath;     // QR file path
    public $qrDataUri;    // inline QR

    public function __construct(Ticket $ticket, $filePath, $qrDataUri)
    {
        $this->ticket    = $ticket;
        $this->filePath  = $filePath;  
        $this->qrDataUri = $qrDataUri;  // IMPORTANT
    }

    public function build()
    {
        return $this->subject('Your Event Ticket - '.$this->ticket->ticket_code)
            ->view('emails.ticket')
            ->attach(Storage::disk('public')->path($this->filePath), [
                'as'   => $this->ticket->ticket_code . '.svg',
                'mime' => 'image/svg+xml',
            ]);
    }
}
