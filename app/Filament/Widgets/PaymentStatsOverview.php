<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class PaymentStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Ambil data pembayaran dengan status 'completed'
        $completedPayments = Payment::where('status', 'completed');

        return [
            Stat::make('Total Pendapatan', 'Rp ' . Number::format($completedPayments->sum('amount'), 0, 0, 'id'))
                ->description('Dari semua transaksi berhasil')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Transaksi Berhasil', $completedPayments->count())
                ->description('Jumlah pembayaran yang selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Transaksi Gagal', Payment::where('status', 'failed')->count())
                ->description('Jumlah pembayaran yang gagal')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}