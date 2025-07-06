<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';
    protected static ?string $navigationLabel = 'Laporan Pembayaran';
    protected static ?string $pluralLabel = 'Laporan Pembayaran';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('order_id')
                ->relationship('order', 'id')
                ->label('Order ID')
                ->disabled()
                ->required()
                ->options(function () {
                    return Order::whereIn('status', ['completed', 'settlement'])->pluck('id', 'id')->toArray();
                })
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $order = Order::find($state);
                    if ($order) {
                        $set('amount', $order->total_amount ?? 0);
                        $set('user_id', $order->user_id);
                        $set('payment_date', now()->format('Y-m-d\TH:i'));
                        $set('status', $order->status);
                    }
                }),

            Select::make('user_id')
                ->relationship('user', 'name')
                ->label('Nama Pelanggan')
                ->disabled()
                ->required(),

            TextInput::make('amount')
                ->numeric()
                ->label('Jumlah Pembayaran')
                ->prefix('Rp')
                ->disabled()
                ->required(),

            Select::make('status')
                ->options([
                    'completed' => 'Completed',
                    'settlement' => 'Settlement',
                    'failed' => 'Failed',
                ])
                ->disabled()
                ->required(),

            TextInput::make('payment_date')
                ->label('Tanggal Pembayaran')
                ->type('datetime-local')
                ->disabled()
                ->required(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('order.id')->label('Order ID'),
                TextColumn::make('user.name')->label('Pelanggan')->sortable()->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah Pembayaran')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state ?? 0, 0, ',', '.')),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed', 'settlement' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->dateTime('d M Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'settlement' => 'Settlement',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('print.payment', ['id' => $record->id]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                BulkAction::make('print_all')
                    ->label('Print Semua')
                    ->icon('heroicon-o-printer')
                    ->url(route('print.semua-pembayaran'))
                    ->openUrlInNewTab(),
            ])
            ->headerActions([
                Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->url(route('print.semua-pembayaran'))
                    ->openUrlInNewTab(),
            ])
            ->query(fn () => Payment::with(['user', 'order']));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
