<?php

namespace App\Filament\Resources;

use App\Models\StockItem;
use App\Models\StockMutation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\StockMutationResource\Pages;
use App\Filament\Resources\StockMutationResource\RelationManagers;
class StockMutationResource extends Resource
{
    protected static ?string $model = StockMutation::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Manajemen Stok';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('stock_item_id')
                    ->relationship('stockItem', 'name')
                    ->required(),
                Select::make('type')
                    ->options([
                        'in' => 'Stock In',
                        'used' => 'Used',
                        'damaged' => 'Damaged',
                        'expired' => 'Expired'
                    ])
                    ->required(),
                TextInput::make('quantity')
                    ->numeric()
                    ->required(),
                Textarea::make('note')
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('stockItem.name'),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'used' => 'warning',
                        'damaged', 'expired' => 'danger',
                    }),
                TextColumn::make('quantity'),
                TextColumn::make('created_at')
                    ->dateTime()
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
            'index' => Pages\ListStockMutations::route('/'),
            'create' => Pages\CreateStockMutation::route('/create'),
            'edit' => Pages\EditStockMutation::route('/{record}/edit'),
        ];
    }
}
