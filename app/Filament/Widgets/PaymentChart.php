<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pendapatan (30 Hari Terakhir)';
    protected static ?int $sort = 2; // Urutan widget

    protected function getData(): array
    {
        $data = Payment::query()
            ->where('status', 'completed')
            ->where('payment_date', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(payment_date) as date'),
                DB::raw('SUM(amount) as aggregate')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data->map(fn ($value) => $value->aggregate),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                ],
            ],
            'labels' => $data->map(fn ($value) => Carbon::parse($value->date)->format('d M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}