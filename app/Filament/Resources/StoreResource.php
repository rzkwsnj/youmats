<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Models\Store;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $navigationGroup = 'Vendors';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        TextInput::make('name')
                            ->rules(REQUIRED_STRING_VALIDATION)
                            ->translatable(),

                        Select::make('vendors')
                            ->options(\App\Models\Vendor::pluck('name', 'id'))
                            ->multiple(),

                        Repeater::make('sub_categories')
                            ->schema([
                                Select::make('category')
                                    ->options(\App\Models\Category::where('parent_id', '>', 0)->pluck('name', 'id')),

                                TextInput::make('store_moq')
                                    ->numeric()
                                    ->minValue(1)
                                    ->rules(NULLABLE_INTEGER_VALIDATION),
                            ]),

                        Repeater::make('contacts')
                            ->schema([
                                TextInput::make('person_name')
                                    ->rules(REQUIRED_STRING_VALIDATION),

                                TextInput::make('email')
                                    ->email()
                                    ->rules(REQUIRED_EMAIL_VALIDATION),

                                PhoneInput::make('call_phone')
                                    ->default('SA')
                                    ->preferredCountries(['SA', 'AE', 'EG'])
                                    ->rules(NULLABLE_NUMERIC_VALIDATION),

                                PhoneInput::make('phone')
                                    ->default('SA')
                                    ->preferredCountries(['SA', 'AE', 'EG'])
                                    ->rules(NULLABLE_NUMERIC_VALIDATION),

                                TextInput::make('phone_code')
                                    ->readonly(),

                                Select::make('cities')
                                    ->options(\App\Models\City::pluck('name', 'id'))
                                    ->multiple(),

                                Select::make('with')
                                    ->options([
                                        'individual' => 'Individual',
                                        'company' => 'Company',
                                        'both' => 'Both'
                                    ])
                            ])

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
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}
