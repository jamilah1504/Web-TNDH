<?php

namespace App\Filament\Resources;

use App\Models\StockItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\StockItemResource\Pages;
use App\Filament\Resources\StockItemResource\RelationManagers;
class StockItemResource extends Resource
{
    protected static ?string $model = StockItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Manajemen Stok';
    protected static ?string $navigationLabel = 'Stok Barang';
    protected static ?string $pluralLabel = 'Stok Barang';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('unit')
                    ->options([
                        'kg' => 'Kilogram',
                        'g' => 'Gram',
                        'pcs' => 'Pieces',
                        'l' => 'Liter',
                        'ml' => 'Milliliter'
                    ])
                    ->required(),
                TextInput::make('min_stock')
                    ->numeric()
                    ->default(0)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('unit'),
                TextColumn::make('min_stock'),
                TextColumn::make('current_stock')
                    ->state(fn (StockItem $record) => $record->current_stock)
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
            'index' => Pages\ListStockItems::route('/'),
            'create' => Pages\CreateStockItem::route('/create'),
            'edit' => Pages\EditStockItem::route('/{record}/edit'),
        ];
    }
}
