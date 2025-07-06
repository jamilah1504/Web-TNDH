<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Manajemen Informasi';


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul Slider'),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('sliders')
                    ->required()
                    ->label('Gambar Slider'),
                Forms\Components\Textarea::make('description')
                    ->nullable()
                    ->label('Deskripsi'),
                Forms\Components\TextInput::make('link')
                    ->url()
                    ->nullable()
                    ->label('Link'),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Aktif?'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()
                    ->sortable()
                    ->label('Judul Slider'),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar Slider'),
                Tables\Columns\TextColumn::make('description')->limit(50)
                    ->label('Deskripsi'),
                Tables\Columns\BooleanColumn::make('is_active')
                    ->label('Aktif?'),
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
