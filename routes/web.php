<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\ScanController;
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

// Admin login page
Route::get('/admin/login', [SessionController::class,'create'])->name('admin.login');

// Login submission
Route::post('/admin/login', [SessionController::class,'adminLogin'])->name('admin.login.post');
Route::get('/admin/login', [SessionController::class, 'create'])->name('login');

// Protected admin pages
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('admin.dashboard.index');

    Route::post('/admin/tickets/{ticket}/invalidate', [TicketController::class,'invalidate'])
        ->name('admin.tickets.invalidate');

    Route::get('/admin/scan', fn() => view('dashboard.scan'))->name('dashboard.scan');

    // Route::get('/admin/scan/check', [ScanController::class, 'check'])->name('admin.scan.check');
    Route::post('/admin/tickets/{ticket}/invalidate', [TicketController::class,'invalidate'])
        ->name('admin.tickets.invalidate');

    Route::post('/logout', [SessionController::class,'destroy'])->name('admin.logout');
});

Route::get('/admin/scan/check', [ScanController::class, 'check'])->name('admin.scan.check');