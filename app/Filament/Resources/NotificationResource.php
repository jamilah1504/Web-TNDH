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
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
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
                FileUpload::make('photo')
                    ->label('Foto')
                    ->image()
                    ->directory('notifications')
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('Aktif?')
                    ->default(true),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->default('-')
                    ->getStateUsing(fn ($record) => $record->product?->name ?? '-'),
                TextColumn::make('title')
                    ->label('Judul'),
                TextColumn::make('message')
                    ->label('Pesan'),
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->default('-')
                    ->getStateUsing(fn ($record) => $record->photo ?? '-'),
                BooleanColumn::make('is_active')
                    ->label('Aktif?'),
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
