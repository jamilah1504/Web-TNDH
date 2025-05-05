<?php

namespace App\Filament\Resources;

use App\Models\Product;
use App\Models\ProductDetail;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\ProductDetailResource\Pages;


class ProductDetailResource extends Resource
{
    protected static ?string $model = ProductDetail::class;
    protected static ?string $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationGroup = 'Manajemen Menu';
    protected static ?string $navigationLabel = 'Menu';
    protected static ?string $pluralLabel = 'Menu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('description')
                    ->maxLength(65535),
                TextInput::make('price')
                    ->numeric()
                    ->required(),
                FileUpload::make('image_url')
                    ->image()
                    ->directory('product-details')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name'),
                TextColumn::make('title'),
                TextColumn::make('price')
                    ->money('IDR'),
                ImageColumn::make('image_url')
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
            'index' => Pages\ListProductDetails::route('/'),
            'create' => Pages\CreateProductDetail::route('/create'),
            'edit' => Pages\EditProductDetail::route('/{record}/edit'),
        ];
    }
}
