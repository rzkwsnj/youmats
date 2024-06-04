<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarTypeResource\Pages;
use App\Models\CarType;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CarTypeResource extends Resource
{
    protected static ?string $model = CarType::class;

    protected static ?string $navigationGroup = 'Tracker';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        \Filament\Forms\Components\TextInput::make('name')
                            ->translatable()
                            ->rules(REQUIRED_STRING_VALIDATION),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name'),
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
            'index' => Pages\ListCarTypes::route('/'),
            'create' => Pages\CreateCarType::route('/create'),
            'edit' => Pages\EditCarType::route('/{record}/edit'),
        ];
    }
}
