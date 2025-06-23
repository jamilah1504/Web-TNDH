<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PaymentStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Status Pembayaran';
    protected static ?int $sort = 3; // Urutan widget

    protected function getData(): array
    {
        $data = Payment::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'datasets' => [
                [
                    'label' => 'Status Pembayaran',
                    'data' => $data->values()->all(),
                    'backgroundColor' => [
                        '#34d399', // Hijau untuk 'completed'
                        '#ef4444', // Merah untuk 'failed'
                    ],
                ],
            ],
            'labels' => $data->keys()->map(fn($status) => ucfirst($status))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}