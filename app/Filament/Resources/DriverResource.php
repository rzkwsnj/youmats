<?php

namespace App\Filament\Resources;

use App\Helpers\Filament\Fields;
use App\Filament\Resources\DriverResource\Pages;
use App\Models\Driver;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationGroup = 'Tracker';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        Select::make('country')
                            ->relationship('country', 'name'),

                        TextInput::make('name')
                            ->rules(REQUIRED_STRING_VALIDATION),

                        TextInput::make('email')
                            ->email()
                            ->rules(REQUIRED_EMAIL_VALIDATION),

                        PhoneInput::make('phone')
                            ->default('SA')
                            ->preferredCountries(['SA', 'AE', 'EG'])
                            ->rules(REQUIRED_STRING_VALIDATION),

                        PhoneInput::make('phone2')
                            ->default('SA')
                            ->preferredCountries(['SA', 'AE', 'EG'])
                            ->rules(NULLABLE_STRING_VALIDATION),

                        PhoneInput::make('whatsapp')
                            ->default('SA')
                            ->preferredCountries(['SA', 'AE', 'EG'])
                            ->rules(NULLABLE_STRING_VALIDATION),

                        Toggle::make('active'),

                        TextInput::make('password'),

                        Fields::image(false, DRIVER_PHOTO, 'Driver Photo', true),

                        Fields::file(false, DRIVER_ID, 'Driver ID', false),

                        Fields::file(false, DRIVER_LICENSE, 'Driver License', false),
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
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
