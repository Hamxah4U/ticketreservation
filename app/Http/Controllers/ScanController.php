<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function check(Request $request)
    {
        $code = $request->code;

        $ticket = Ticket::where('ticket_code', $code)->first();

        if(!$ticket){
            return [
                'status' => 'danger',
                'message' => '❌ Ticket does not exist'
            ];
        }

        if(!$ticket->is_valid){
            return [
                'status' => 'warning',
                'message' => '⚠ Ticket already used or invalid'
            ];
        }

        // Mark ticket as used
        $ticket->update(['is_valid' => 0]);

        return [
            'status' => 'success',
            'message' => '✅ Valid Ticket — Access Granted!'
        ];
    }
}