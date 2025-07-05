<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Filament\Widgets\PaymentChart;
use App\Filament\Widgets\PaymentStatsOverview;
use App\Filament\Widgets\PaymentStatusChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    

    protected function getHeaderActions(): array
    {
        // Hapus tombol "Create" bawaan jika ada
        return [];
    }
}