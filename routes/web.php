<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\VerificationController;

Route::get('/', function() {
    return redirect()->route('tickets.index');
});

// purchase form
Route::get('/buy', [TicketController::class,'showPurchaseForm'])->name('tickets.buy');
Route::post('/buy', [TicketController::class,'createAndInitPayment'])->name('tickets.create');

// Paystack
Route::get('/paystack/init', [PaystackController::class,'init'])->name('paystack.init');
Route::get('/paystack/callback', [PaystackController::class,'callback'])->name('paystack.callback');

// verification endpoint used by scanner
Route::get('/verify', [VerificationController::class,'verify'])->name('tickets.verify');


