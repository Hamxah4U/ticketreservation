<?php

namespace App\Http\Controllers;

use Yabacon\Paystack;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Mail\TicketMail;
use Exception;

class PaystackController extends Controller
{
    protected $paystack;

    public function __construct()
    {
        $secret = config('services.paystack.secret') ?? env('PAYSTACK_SECRET_KEY');
        $this->paystack = new Paystack($secret);

        // Ensure the temp QR folder exists
        if (!Storage::disk('local')->exists('qr_temp')) {
            Storage::disk('local')->makeDirectory('qr_temp');
        }
    }

    // Initialize transaction
    public function init(Request $request)
    {
        $reference = $request->query('reference');
        $payment = Payment::where('reference', $reference)->firstOrFail();
        $ticket = $payment->ticket;

        $callback_url = route('paystack.callback');

        $init = $this->paystack->transaction->initialize([
            'amount' => (int) ($payment->amount * 100),
            'email' => $ticket->buyer_email,
            'reference' => $payment->reference,
            'callback_url' => $callback_url,
            'channel' => ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer', 'pos', 'eft', 'payattitude', 'barter', 'open_banking', 'mobile_money_rwanda', 'mobile_money_uganda', 'mobile_money_zambia', 'mobile_money_tanzania'],
            'metadata' => [
                'ticket_id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
            ],
        ]);

        if (!isset($init->data->authorization_url)) {
            return redirect()->route('tickets.buy')->with('error', 'Unable to initialize payment');
        }

        return redirect($init->data->authorization_url);
    }

    // Callback from Paystack
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('tickets.buy')->with('error', 'No reference provided');
        }

        $payment = Payment::where('reference', $reference)->firstOrFail();

        // Verify transaction
        $verify = $this->paystack->transaction->verify([
            'reference' => $reference,
        ]);

        if ($verify->data->status === 'success') {
            // Mark payment success
            $payment->update([
                'status' => 'success',
                'meta' => json_encode($verify->data),
            ]);

            $ticket = $payment->ticket;

            // Generate QR code (PNG fallback)
            try {
                $pngBinary = QrCode::format('png')
                    ->size(300)
                    ->errorCorrection('H')
                    ->generate($ticket->qr_payload);

                $dataUri = 'data:image/png;base64,' . base64_encode($pngBinary);
            } catch (Exception $e) {
                // Fallback SVG
                $svg = QrCode::format('svg')
                    ->size(300)
                    ->errorCorrection('H')
                    ->generate($ticket->qr_payload);

                $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
            }

            // Send ticket email
            Mail::to($ticket->buyer_email)->send(new TicketMail($ticket, $dataUri));

            return view('tickets.success', [
                'ticket' => $ticket,
                'dataUri' => $dataUri
            ]);

        } else {
            // Payment failed
            $payment->update([
                'status' => 'failed',
                'meta' => json_encode($verify->data),
            ]);

            return redirect()->route('tickets.buy')->with('error', 'Payment failed.');
        }
    }
}

