<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class WidgetOrderCountChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Order per Bulan';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $year = now()->year;

        $monthlyOrders = Order::query()
            ->selectRaw('MONTH(updated_at) as month, COUNT(*) as total')
            ->where('status', 'completed')
            ->whereYear('updated_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $orders = collect(range(1, 12))->map(function ($month) use ($monthlyOrders) {
            return $monthlyOrders->get($month, 0);
        });

        $labels = collect(range(1, 12))->map(
            fn ($month) => Carbon::create($year, $month, 1)->translatedFormat('F')
        );

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Order per Bulan',
                    'data' => $orders->toArray(),
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
                        'ticks' => [
                            'stepSize' => 1,
                        ],
                    ],
                ],
            ],
        ];
    }
}
