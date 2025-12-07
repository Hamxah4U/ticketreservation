<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function check(Request $request)
    {
        try {
            $code = $request->query('code');

            if (!$code) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Missing Code',
                    'message' => 'Ticket code is required.'
                ]);
            }

            $ticket = Ticket::where('ticket_code', $code)->first();

            if (!$ticket) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Invalid Ticket',
                    'message' => 'Ticket code not found.'
                ]);
            }

            if ($ticket->status === 'valid') {
                $ticket->update([
                    'status' => 'used',
                    'used_at' => now(),
                ]);

                return response()->json([
                    'status' => 'success',
                    'title' => 'Valid Ticket',
                    'message' => "Welcome {$ticket->buyer_name}! Seat: {$ticket->seat_number}.",
                    'used_at' => optional($ticket->used_at)->format('M d, Y h:i A')
                ]);
            }

            if ($ticket->status === 'used') {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Used Ticket',
                    'message' => "{$ticket->buyer_name} has already used this ticket.",
                    'used_at' => optional($ticket->used_at)->format('M d, Y h:i A')
                ]);
            }

            return response()->json([
                'status' => 'error',
                'title' => 'Error',
                'message' => 'Unexpected ticket status.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'title' => 'Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
