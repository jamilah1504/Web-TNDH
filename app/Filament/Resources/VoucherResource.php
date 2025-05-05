<?php

namespace App\Filament\Resources;

use App\Models\Voucher;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\VoucherResource\Pages;


class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                TextInput::make('discount')
                    ->numeric()
                    ->required(),
                Radio::make('discount_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount'
                    ])
                    ->required(),
                DatePicker::make('valid_from')
                    ->required(),
                DatePicker::make('valid_to')
                    ->required(),
                TextInput::make('usage_limit')
                    ->numeric(),
                TextInput::make('used_count')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->default(true)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('discount'),
                TextColumn::make('discount_type'),
                TextColumn::make('valid_from')
                    ->date(),
                TextColumn::make('valid_to')
                    ->date(),
                TextColumn::make('is_active')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
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
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}
