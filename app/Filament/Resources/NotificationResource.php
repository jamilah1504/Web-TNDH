<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Models\Notification;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationGroup = 'Manajemen Informasi';
     protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Notifikasi';
    protected static ?string $pluralLabel = 'Notifikasi';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'customer' => 'Customer',
                    ])
                    ->required(),
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Produk')
                    ->nullable(),
                TextInput::make('title')
                    ->label('Judul')
                    ->required(),
                Textarea::make('message')
                    ->label('Pesan')
                    ->required(),
                Toggle::make('is_read')
                    ->label('Sudah Dibaca?')
                    ->default(false),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('role')->label('Role'),
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->default('-')
                    ->getStateUsing(fn ($record) => $record->product?->name ?? '-'),
                TextColumn::make('title')->label('Judul'),
                TextColumn::make('message')->label('Pesan'),
                BooleanColumn::make('is_read')->label('Sudah Dibaca?'),
                TextColumn::make('users_count')
                    ->label('Jumlah Pengguna')
                    ->getStateUsing(fn ($record) => $record->users()->count()),
            ])
            ->filters([
                //
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
