<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class TopUsersByOrdersWidget extends TableWidget
{
    protected static ?string $heading = 'Top Pengguna dengan Pemesanan Terbanyak';

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return User::query()
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.id', 'users.name', \DB::raw('COUNT(orders.id) as order_count'))
            ->where('orders.status', 'completed')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('order_count')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nama Pengguna')
                ->searchable()
                ->sortable(),
            TextColumn::make('order_count')
                ->label('Jumlah Pesanan')
                ->sortable(),
        ];
    }

    public function getTableRecordKey($record): string // Ubah ke public
    {
        return (string) $record->id; // Menggunakan id pengguna sebagai kunci unik
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'order_count';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}
