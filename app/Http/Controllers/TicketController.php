<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Exception;

class TicketController extends Controller
{
    public function showPurchaseForm()
    {
        return view('tickets.index');
    }

    public function createAndInitPayment(Request $request)
    {
        $data = $request->validate([
            'buyer_name' => 'required|string|max:255',
            'buyer_email' => 'required|email|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $ticket_code = 'EVT' . strtoupper(Str::random(8));

        $ticket = Ticket::create([
            'ticket_code' => $ticket_code,
            'buyer_name' => $data['buyer_name'],
            'buyer_email' => $data['buyer_email'],
            'price' => $data['price'],
            'qr_payload' => $ticket_code,
            'status' => 'valid',
            'qrcodesvg' => $ticket_code.'svg'
        ]);

        $reference = 'ref_' . Str::random(12);
        Payment::create([
            'reference' => $reference,
            'ticket_id' => $ticket->id,
            'amount' => $data['price'],
            'status' => 'pending',
        ]);

        return redirect(route('paystack.init', ['reference' => $reference]));
    }

    public function sendTicketEmail(Ticket $ticket)
    {
        try {
            $pngBinary = QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->generate($ticket->qr_payload);

            $dataUri = 'data:image/png;base64,' . base64_encode($pngBinary);
        } catch (Exception $e) {
            $svg = QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate($ticket->qr_payload);

            $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
        }

        Mail::to($ticket->buyer_email)->send(new \App\Mail\TicketMail($ticket, $dataUri));
    }
}
