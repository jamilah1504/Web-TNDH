<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pendapatan (30 Hari Terakhir)';
    protected static ?int $sort = 1;
    protected static ?int $columns = 6; // Adjusted to 6 for half-width

    protected const BLUE = '#3b82f6';
    protected const BLUE_BG = 'rgba(59, 130, 246, 0.2)';

    protected function getData(): array
    {
        $records = Payment::query()
            ->where('status', 'completed')
            ->where('payment_date', '>=', now()->subDays(30))
            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = $records->map(fn($r) => Carbon::parse($r->date)->translatedFormat('d M'))->toArray();
        $totals = $records->pluck('total')->toArray();

        return [
            'datasets' => [[
                'label' => 'Pendapatan Harian',
                'data' => $totals,
                'fill' => false,
                'borderColor' => self::BLUE,
                'backgroundColor' => self::BLUE_BG,
                'tension' => 0.4,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
