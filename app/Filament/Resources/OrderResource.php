<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Payment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;

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
                            TextInput::make('id')
                                ->label('ID Pesanan')
                                ->disabled()
                                ->default(fn () => (string) Str::uuid())
                                ->unique()
                                ->dehydrated(true),

                            Select::make('user_id')
                                ->relationship('user', 'name')
                                ->label('Nama Pelanggan')
                                ->required()
                                ->searchable()
                                ->preload(),

                            TextInput::make('total_amount')
                                ->numeric()
                                ->label('Total Pembayaran')
                                ->disabled()
                                ->prefix('Rp')
                                ->default(0)
                                ->dehydrated(true),

                            Select::make('status')
                                ->options([
                                    'pending' => 'Pending',
                                    'processing' => 'Processing',
                                    'shipped' => 'Shipped',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->required()
                                ->default('pending'),

                            TextInput::make('created_at')
                                ->label('Tanggal Pesanan')
                                ->type('datetime-local')
                                ->disabled()
                                ->default(now()),
                        ]),
                    ]),

                Section::make('Detail Item Pesanan')
                    ->schema([
                        Repeater::make('orderItems')
                            ->relationship()
                            ->label('Item Produk')
                            ->schema([
                                Grid::make(4)->schema([
                                    Select::make('product_id')
                                        ->relationship('product', 'name')
                                        ->label('Produk')
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                // Calculate price as price - discount_price (if discount_price exists)
                                                $price = $product->discount_price !== null ? $product->price - $product->discount_price : $product->price ?? 0;
                                                $set('price', $price);
                                                $quantity = $get('quantity') ?? 1;
                                                $set('subtotal', $price * $quantity);
                                            }
                                            // Update total_amount
                                            $orderItems = $get('../../orderItems') ?? [];
                                            $total = array_sum(array_column($orderItems, 'subtotal'));
                                            $set('../../total_amount', $total);
                                        }),

                                    TextInput::make('quantity')
                                        ->label('Jumlah')
                                        ->numeric()
                                        ->required()
                                        ->minValue(1)
                                        ->default(1)
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            $price = $get('price') ?? 0;
                                            $set('subtotal', $price * $state);
                                            // Update total_amount
                                            $orderItems = $get('../../orderItems') ?? [];
                                            $total = array_sum(array_column($orderItems, 'subtotal'));
                                            $set('../../total_amount', $total);
                                        }),

                                    TextInput::make('price')
                                        ->label('Harga Satuan')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->disabled()
                                        ->default(0)
                                        ->dehydrated(true),

                                    TextInput::make('subtotal')
                                        ->label('Subtotal')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->disabled()
                                        ->default(0)
                                        ->dehydrated(false), // Subtotal is not stored, only for display
                                ])
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Item')
                            ->columnSpanFull(),
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
                    'challenge' => 'warning',
                    'expired' => 'danger',
                    'denied' => 'danger',
                }),
                TextColumn::make('created_at')->label('Tanggal')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        if (!isset($data['id']) || empty($data['id'])) {
            $data['id'] = (string) Str::uuid();
        }

        // Ensure total_amount is updated based on orderItems
        $orderItems = $data['orderItems'] ?? [];
        $total = array_sum(array_column($orderItems, 'subtotal'));
        $data['total_amount'] = $total;

        // Create or update payment record
        if (in_array($data['status'], ['completed', 'settlement'])) {
            Payment::updateOrCreate(
                ['order_id' => $data['id']],
                [
                    'user_id' => $data['user_id'],
                    'amount' => $data['total_amount'],
                    'status' => $data['status'],
                    'payment_date' => now(),
                ]
            );
        }

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        // Update total_amount when saving (edit)
        $orderItems = $data['orderItems'] ?? [];
        $total = array_sum(array_column($orderItems, 'subtotal'));
        $data['total_amount'] = $total;

        // Update payment record if status is completed or settlement
        if (in_array($data['status'], ['completed', 'settlement'])) {
            Payment::updateOrCreate(
                ['order_id' => $data['id']],
                [
                    'user_id' => $data['user_id'],
                    'amount' => $data['total_amount'],
                    'status' => $data['status'],
                    'payment_date' => now(),
                ]
            );
        }

        return $data;
    }
}
