<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class WidgetAIncomeChart extends ChartWidget
{
    protected static ?string $heading = 'Pembayaran per Bulan';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $year = now()->year;

        $monthlyIncomes = Payment::query()
            ->selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
            ->whereYear('payment_date', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $incomes = collect(range(1, 12))->map(function ($month) use ($monthlyIncomes) {
            return $monthlyIncomes->get($month, 0);
        });

        $labels = collect(range(1, 12))->map(
            fn ($month) => Carbon::create($year, $month, 1)->translatedFormat('F')
        );

        return [
            'datasets' => [
                [
                    'label' => 'Total Pembayaran per Bulan',
                    'data' => $incomes->toArray(),
                    'fill' => true,
                    'borderColor' => '#34d399',
                    'backgroundColor' => 'rgba(52, 211, 153, 0.2)',
                ],
            ],
            'labels' => $labels->toArray(),
            'options' => [
                'scales' => [
                    'y' => [
                        'min' => 0,
                        'max' => 5000000,
                        'ticks' => [
                            'stepSize' => 500000,
                        ],
                    ],
                ],
            ],
        ];
    }
}
