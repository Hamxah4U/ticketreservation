<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VerificationController extends Controller
{
    // route: /verify?code=EVTXXXX
    public function verify(Request $request)
    {
        $code = $request->query('code');
        if(!$code) {
            return response()->json(['status'=>'error','message'=>'No code provided.'], 400);
        }

        // Use transaction + where status valid to avoid race conditions
        $updated = DB::transaction(function() use ($code) {
            // find ticket with status valid
            $ticket = Ticket::where('ticket_code', $code)
                ->where('status', 'valid')
                ->lockForUpdate() // optional in some DBs
                ->first();

            if(!$ticket) {
                return null;
            }

            $ticket->status = 'used';
            $ticket->used_at = Carbon::now();
            $ticket->save();

            return $ticket;
        });

        if(!$updated) {
            // Check why: either invalid code or already used
            $ticket = Ticket::where('ticket_code', $code)->first();
            if(!$ticket) {
                return response()->json(['status'=>'invalid','message'=>'Ticket code is invalid.'], 404);
            }

            if($ticket->status == 'used') {
                return response()->json(['status'=>'used','message'=>'Ticket already used at '.$ticket->used_at], 409);
            }

            return response()->json(['status'=>'error','message'=>'Unable to validate ticket.'], 500);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Access granted. Welcome!',
            'ticket_code' => $updated->ticket_code,
            'buyer' => $updated->buyer_name,
        ]);
    }
}
