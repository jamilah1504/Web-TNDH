<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Manajemen Menu';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $pluralLabel = 'Produk';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('id_category')
                    ->relationship('category', 'name_category')
                    ->label('Kategori')
                    ->required(),
                TextInput::make('name')
                    ->label('Nama Produk')
                    ->required(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->nullable(),
                TextInput::make('excerpt')
                    ->label('Ringkasan')
                    ->required(),
                TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->required()
                    ->step(0.01),
                TextInput::make('discount_price')
                    ->label('Harga Diskon')
                    ->numeric()
                    ->nullable()
                    ->step(0.01),
                Select::make('stock_status')
                    ->label('Status Stok')
                    ->options([
                        'available' => 'Tersedia',
                        'out_of_stock' => 'Habis'
                    ])
                    ->default('available')
                    ->required()
                    ->rules(['in:available,out_of_stock'])
                    ->live() // Make reactive for form interactions
                    ->afterStateUpdated(function ($state) {
                        \Illuminate\Support\Facades\Log::info('Stock Status updated in form: ' . $state);
                    }),
                FileUpload::make('image')
                    ->label('Gambar Produk')
                    ->image()
                    ->directory('products')
                    ->nullable()
                    ->default('default.jpg'),
                Toggle::make('is_available')
                    ->label('Masih Dijual?')
                    ->default(true)
                    ->live() // Make reactive for form interactions
                    ->afterStateUpdated(function ($state) {
                        \Illuminate\Support\Facades\Log::info('Is Available updated in form: ' . $state);
                    }),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('category.name_category')
                    ->label('Kategori'),
                TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('discount_price')
                    ->label('Harga Diskon')
                    ->formatStateUsing(fn ($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : '-')
                    ->sortable(),
                TextColumn::make('stock_status_display')
                    ->label('Status Stok'),
                TextColumn::make('is_available_display')
                    ->label('Status Penjualan'),
                ImageColumn::make('image')
                    ->label('Gambar'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function () {
                        // Force table refresh after edit
                        \Illuminate\Support\Facades\Log::info('Product edited, table should refresh');
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
