<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        // Ambil filter tanggal dari page filters
        $startDate = !is_null($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : Carbon::createFromTimestamp(0);

        $endDate = !is_null($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : now();

        // Hitung data statis (tidak pakai chart)
        $totalUsers = User::count();
        $totalCategories = Category::count();
        $totalProducts = Product::count();
        $totalReviews = Review::count();

        // Hitung data dengan chart (berdasarkan rentang waktu)
        $paymentData = Payment::query()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $orderData = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Gabungkan semua tanggal untuk chart
        $dates = array_unique(array_merge(array_keys($paymentData), array_keys($orderData)));
        sort($dates);

        $paymentChart = [];
        $orderChart = [];

        foreach ($dates as $date) {
            $paymentChart[] = $paymentData[$date] ?? 0;
            $orderChart[] = $orderData[$date] ?? 0;
        }

        $totalPayments = array_sum($paymentChart);
        $totalOrders = array_sum($orderChart);

        return [
            Stat::make('Total Pembayaran', 'Rp. ' . number_format($totalPayments, 2, ',', '.'))
                ->description('Total pembayaran dari user')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($paymentChart)
                ->color('success'),

            Stat::make('Total Pengguna', number_format($totalUsers))
                ->description('Total pengguna terdaftar')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Total Order', number_format($totalOrders))
                ->description('Jumlah pesanan yang dibuat')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->chart($orderChart)
                ->color('teal'),

            Stat::make('Total Kategori', number_format($totalCategories))
                ->description('Jumlah kategori makanan')
                ->descriptionIcon('heroicon-o-rectangle-stack')
                ->color('warning'),

            Stat::make('Total Produk', number_format($totalProducts))
                ->description('Jumlah produk yang tersedia')
                ->descriptionIcon('heroicon-o-cube')
                ->color('info'),

            Stat::make('Total Review', number_format($totalReviews))
                ->description('Ulasan yang diberikan pelanggan')
                ->descriptionIcon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('gray'),
        ];
    }
}
