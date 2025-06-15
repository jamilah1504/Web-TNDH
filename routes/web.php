<?php

use Illuminate\Support\Facades\Route;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return view('home');
});
// Route::get('/payments/income-report', function () {
//     $payments = Payment::where('status', 'completed')
//         ->with(['user', 'order'])
//         ->get();
//     $totalIncome = $payments->sum('amount');

//     $pdf = Pdf::loadView('pdf.income_report', compact('payments', 'totalIncome'));
//     return $pdf->stream('income-report-' . now()->format('Y-m-d') . '.pdf');
// })->name('payment.income.report');
