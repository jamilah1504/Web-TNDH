<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Models\User;
use App\Models\Payment;
use App\Models\Order;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        // Ambil filter tanggal
        $startDate = !is_null($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : Carbon::createFromTimestamp(0);

        $endDate = !is_null($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : now();

        // Total user (tidak pakai chart)
        $totalUsers = User::count();

        // Data pembayaran per hari
        $paymentData = Payment::query()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Data order per hari
        $orderData = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Gabungkan semua tanggal
        $dates = array_unique(array_merge(array_keys($paymentData), array_keys($orderData)));
        sort($dates);

        // Siapkan chart array
        $paymentChart = [];
        $orderChart = [];

        foreach ($dates as $date) {
            $paymentChart[] = $paymentData[$date] ?? 0;
            $orderChart[] = $orderData[$date] ?? 0;
        }

        // Hitung total
        $totalPayments = array_sum($paymentChart);
        $totalOrders = array_sum($orderChart);

        return [
            Stat::make('Total Pengguna', number_format($totalUsers))
                ->description('Total pengguna terdaftar')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Total Pembayaran', 'Rp. ' . number_format($totalPayments, 2, ',', '.'))
                ->description('Total pembayaran yang diterima')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($paymentChart)
                ->color('success'),

            Stat::make('Total Order', number_format($totalOrders))
                ->description('Jumlah pesanan yang dibuat')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->chart($orderChart)
                ->color('info'),
        ];
    }
}
