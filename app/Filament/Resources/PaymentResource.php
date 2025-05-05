<?php

namespace App\Filament\Resources;

use App\Models\Payment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\PaymentResource\Pages;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';
    protected static ?string $navigationLabel = 'Pembayaran';
    protected static ?string $pluralLabel = 'Pembayaran';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('order_id')
                    ->relationship('order', 'order_number')
                    ->required(),
                TextInput::make('amount')
                    ->numeric()
                    ->required(),
                Select::make('payment_method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'e-wallet' => 'E-Wallet',
                        'cash' => 'Cash'
                    ])
                    ->required(),
                Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed'
                    ])
                    ->required(),
                TextInput::make('transaction_id')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number'),
                TextColumn::make('amount')
                    ->money('IDR'),
                TextColumn::make('payment_method'),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
