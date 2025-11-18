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

        // Ensure public storage qrcodes folder exists
        if (!Storage::disk('public')->exists('qrcodes')) {
            Storage::disk('public')->makeDirectory('qrcodes');
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
            'email'  => $ticket->buyer_email,
            'reference' => $payment->reference,
            'callback_url' => $callback_url,
            'channel' => [
                'card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer',
                'pos', 'eft', 'payattitude', 'barter', 'open_banking',
                'mobile_money_rwanda', 'mobile_money_uganda',
                'mobile_money_zambia', 'mobile_money_tanzania'
            ],
            'metadata' => [
                'ticket_id'   => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
            ],
        ]);

        if (!isset($init->data->authorization_url)) {
            return redirect()->route('tickets.buy')
                ->with('error', 'Unable to initialize payment');
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

        // Verify payment
        $verify = $this->paystack->transaction->verify([
            'reference' => $reference,
        ]);

        if ($verify->data->status !== 'success') {
            $payment->update([
                'status' => 'failed',
                'meta'   => json_encode($verify->data),
            ]);

            return redirect()->route('tickets.buy')->with('error', 'Payment failed.');
        }

        // Payment success
        $payment->update([
            'status' => 'success',
            'meta'   => json_encode($verify->data),
        ]);

        $ticket = $payment->ticket;


        /**
         * ----------------------------
         *  GENERATE + SAVE QR CODE
         * ----------------------------
         */
        $fileName = $ticket->ticket_code . '.png';
        $filePath = 'qrcodes/' . $fileName;

        try {
            // Generate PNG QR
            $qrPng = QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->generate($ticket->qr_payload);

            // Save to public/storage/qrcodes/
            Storage::disk('public')->put($filePath, $qrPng);

            // Base64 for email & view
            $dataUri = 'data:image/png;base64,' . base64_encode($qrPng);

        } catch (Exception $e) {
            \Log::error("QR PNG failed: " . $e->getMessage());

            // SVG fallback
            try {
                $qrSvg = QrCode::format('svg')
                    ->size(300)
                    ->errorCorrection('H')
                    ->generate($ticket->qr_payload);

                $fileName = $ticket->ticket_code . '.svg';
                $filePath = 'qrcodes/' . $fileName;

                Storage::disk('public')->put($filePath, $qrSvg);

                $dataUri = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

            } catch (Exception $e2) {
                \Log::error("QR SVG failed: " . $e2->getMessage());
                return redirect()->route('tickets.buy')->with('error', 'QR Code generation failed.');
            }
        }


        /**
         * ----------------------------
         *  SEND EMAIL WITH QR
         * ----------------------------
         */
        
        // Mail::to($ticket->buyer_email)->send(new TicketMail($ticket, $filePath));
        try {
            Mail::to($ticket->buyer_email)->send(new TicketMail($ticket, $filePath, $dataUri));
        } catch (Exception $e) {
            \Log::error("Email sending failed: " . $e->getMessage());
            // Optionally, you can choose to notify the user about email failure here
        }
        

        /**
         * ----------------------------
         *  RETURN SUCCESS VIEW
         * ----------------------------
         */
        return view('tickets.success', [
            'ticket' => $ticket,
            'dataUri' => $dataUri
        ]);
    }
}
