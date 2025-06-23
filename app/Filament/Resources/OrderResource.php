<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater; // <-- Import Repeater
use Filament\Forms\Components\Grid; // <-- Import Grid untuk layout
use Filament\Forms\Components\Section; // <-- Import Section untuk grouping

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';
    protected static ?string $navigationLabel = 'Pesanan';
    protected static ?string $pluralLabel = 'Pesanan';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pesanan')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('user_id')
                                ->relationship('user', 'name')
                                ->label('Nama Pelanggan')
                                ->disabled(),

                            TextInput::make('total_amount')
                                ->numeric()
                                ->label('Total Pembayaran')
                                ->disabled()
                                ->prefix('Rp'),

                            Select::make('status')
                                ->options([
                                    'pending' => 'Pending',
                                    'processing' => 'Processing',
                                    'shipped' => 'Shipped',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->required(),
                            
                            TextInput::make('created_at')
                                ->label('Tanggal Pesanan')
                                ->disabled(),
                        ]),
                    ]),

                Section::make('Detail Item Pesanan')
                    ->schema([
                        Repeater::make('orderItems') // <-- Nama relasi di model Order
                            ->relationship()
                            ->label('Item Produk')
                            ->schema([
                                Grid::make(4)->schema([
                                    // Menggunakan relasi orderItems.product untuk mengambil nama
                                    Select::make('product_id')
                                        ->relationship('product', 'name')
                                        ->label('Produk')
                                        ->disabled(),

                                    TextInput::make('quantity')
                                        ->label('Jumlah')
                                        ->numeric()
                                        ->disabled(),

                                    TextInput::make('price')
                                        ->label('Harga Satuan')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->disabled(),

                                    // Menghitung subtotal
                                    TextInput::make('subtotal')
                                        ->label('Subtotal')
                                        ->numeric()
                                        ->prefix('Rp')
                                        // Mengisi nilai subtotal secara dinamis
                                        ->formatStateUsing(fn ($record) => $record ? $record->quantity * $record->price : 0)
                                        ->disabled(),
                                ])
                            ])
                            // Pengaturan untuk Repeater
                            ->columns(1)
                            ->addable(false) // Sembunyikan tombol "Add"
                            ->deletable(false) // Sembunyikan tombol "Delete"
                            ->disabled() // Buat seluruh repeater read-only
                            ->collapsible() // Agar bisa di-collapse
                            ->collapsed(), // Default dalam keadaan ter-collapse
                    ])->collapsible(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order ID')->sortable(),
                TextColumn::make('user.name')->label('Pelanggan')->sortable()->searchable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'processing' => 'info',
                    'shipped' => 'primary',
                    'completed' => 'success',
                    'settlement' => 'success',
                    'cancelled' => 'danger',
                }),
                TextColumn::make('created_at')->label('Tanggal')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc') // Mengurutkan dari yang terbaru
            ->actions([
                Tables\Actions\ViewAction::make(), // <-- Tambahkan ini
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // Tambahkan halaman View jika ingin memisahkannya dari Edit
            'view' => Pages\ViewOrder::route('/{record}'), 
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Tetap disable create action
    }
}