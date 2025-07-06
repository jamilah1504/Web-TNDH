<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PaymentStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Status Pembayaran';
    protected static ?int $sort = 2;
    protected static ?int $columns = 6; // Half-width, same as PaymentChart

    protected const GREEN = '#34d399';
    protected const RED = '#ef4444';

    protected function getData(): array
    {
        $data = Payment::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $labels = $data->keys()->map(fn($status) => ucfirst($status))->all();
        $values = $data->values()->all();

        $colors = collect($labels)->map(function ($label) {
            return $label === 'Completed' ? self::GREEN : self::RED;
        })->all();

        return [
            'datasets' => [[
                'label' => 'Status Pembayaran',
                'data' => $values,
                'backgroundColor' => $colors,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getRowHeight(): ?int
    {
        return 2; // Match the default row height of the line chart (adjust as needed)
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false, // Allow custom height control
            'responsive' => true,
            'aspectRatio' => 1.4, // Adjust aspect ratio to make it more compact (e.g., wider than tall)
        ];
    }
}
