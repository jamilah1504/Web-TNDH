<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Manajemen Informasi';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->disabled()
                    ->label('Nama Pelanggan'),
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->disabled()
                    ->label('Produk'),
                TextInput::make('rating')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->disabled(),
                Textarea::make('comment')->disabled()
                    ->label('Komentar'),
                Toggle::make('is_approved')->label('Approved'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->sortable()->searchable()
                    ->label('Nama Pelanggan'),
                TextColumn::make('product.name')->sortable()->searchable()
                    ->label('Produk'),
                TextColumn::make('rating'),
                TextColumn::make('comment')->limit(50)
                    ->label('Komentar'),
                BooleanColumn::make('is_approved')->label('Approved'),
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
            'index' => Pages\ListReviews::route('/')
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Disable create action
    }
}
