<?php

namespace App\Filament\Resources;

use App\Helpers\Filament\Fields;
use App\Filament\Resources\CarResource\Pages;
use App\Models\Car;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationGroup = 'Tracker';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        Select::make('Driver')
                            ->relationship('Driver', 'name'),

                        Select::make('type')
                            ->relationship('type', 'name'),

                        TextInput::make('name')
                            ->rules(NULLABLE_STRING_VALIDATION)
                            ->translatable(),

                        TextInput::make('model')
                            ->rules(REQUIRED_STRING_VALIDATION)
                            ->required(),

                        TextInput::make('license_no')
                            ->rules(REQUIRED_STRING_VALIDATION)
                            ->required(),

                        TextInput::make('max_load')
                            ->numeric()
                            ->minValue(0)
                            ->rules(REQUIRED_INTEGER_VALIDATION)
                            ->required()
                            ->helperText('Weight / Tons'),

                        TextInput::make('price_per_kilo')
                            ->numeric()
                            ->minValue(0)
                            ->rules(REQUIRED_NUMERIC_VALIDATION)
                            ->helperText('Price Per Kilo/Meter'),

                        Toggle::make('active'),

                        Fields::image(false, CAR_PHOTO, 'Car Photo', false),

                        Fields::file(false, CAR_LICENSE, 'Car License', false),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\SelectColumn::make('Driver'),
                Tables\Columns\SelectColumn::make('type'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('model'),
                Tables\Columns\ToggleColumn::make('active'),
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
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
}
