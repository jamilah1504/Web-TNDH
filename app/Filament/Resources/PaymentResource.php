<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';
    protected static ?string $navigationLabel = 'Pembayaran';
    protected static ?string $pluralLabel = 'Pembayaran';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->disabled(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->disabled(),
                TextInput::make('amount')
                    ->numeric()
                    ->disabled()
                    ->prefix('Rp'),
                Select::make('status')
                    ->options(['completed' => 'Completed', 'failed' => 'Failed'])
                    ->required(),
                TextInput::make('payment_date')
                    ->type('datetime-local')
                    ->disabled(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')->label('Order ID'),
                TextColumn::make('user.name')->sortable()->searchable(),
                TextColumn::make('amount')->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('status'),
                TextColumn::make('payment_date')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
        return false; // Disable create action
    }
}