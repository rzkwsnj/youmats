<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NovaSettingsResource\Pages;
use App\Models\NovaSettings;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class NovaSettingsResource extends Resource
{
    protected static ?string $model = NovaSettings::class;

    protected static ?string $navigationGroup = 'General-Settings';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([
                        TextInput::make('key'),

                        TextInput::make('value')
                            ->translatable(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('key'),
                TextColumn::make('value'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNovaSettings::route('/'),
            'create' => Pages\CreateNovaSettings::route('/create'),
            'edit' => Pages\EditNovaSettings::route('/{record}/edit'),
        ];
    }
}
