<?php

use Illuminate\Support\Facades\Route;
use App\Models\Payment;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return view('home');
});

// Cetak 1 invoice pembayaran
Route::get('/print/payment/{id}', function ($id) {
    $payment = Payment::with(['user', 'order'])->findOrFail($id);
    $pdf = Pdf::loadView('payments.print', compact('payment'));
    return $pdf->download('payment-' . $payment->id . '.pdf');
})->name('print.payment');

// Cetak semua pembayaran
Route::get('/print/semua-pembayaran', function () {
    $payments = Payment::with(['user', 'order'])->get();
    $pdf = Pdf::loadView('payments.print_all', compact('payments'));
    return $pdf->download('semua-pembayaran.pdf');
})->name('print.semua-pembayaran');
