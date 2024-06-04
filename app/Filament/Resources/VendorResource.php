<?php

namespace App\Filament\Resources;

use App\Helpers\Filament\Fields;
use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationGroup = 'Vendors';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([
                        Select::make('country')
                            ->relationship('country', 'name'),

                        TextInput::make('name')
                            ->rules(REQUIRED_STRING_VALIDATION)
                            ->translatable(),

                        TextInput::make('email')
                            ->email()
                            ->rules(REQUIRED_EMAIL_VALIDATION),

                        PhoneInput::make('call_phone')
                            ->default('SA')
                            ->preferredCountries(['SA', 'AE', 'EG'])

                            ->rules(NULLABLE_STRING_VALIDATION),

                        PhoneInput::make('phone')
                            ->default('SA')
                            ->preferredCountries(['SA', 'AE', 'EG'])

                            ->rules(NULLABLE_STRING_VALIDATION),

                        TextInput::make('address')
                            ->rules(NULLABLE_STRING_VALIDATION),

                        Repeater::make('contacts')
                            ->schema([
                                TextInput::make('person_name')
                                    ->rules(REQUIRED_STRING_VALIDATION),

                                TextInput::make('email')
                                    ->rules(REQUIRED_EMAIL_VALIDATION),

                                PhoneInput::make('call_phone')
                                    ->default('SA')
                                    ->preferredCountries(['SA', 'AE', 'EG'])
                                    ->rules(NULLABLE_NUMERIC_VALIDATION),

                                PhoneInput::make('phone')
                                    ->default('SA')
                                    ->preferredCountries(['SA', 'AE', 'EG'])
                                    ->rules(NULLABLE_NUMERIC_VALIDATION),

                                PhoneInput::make('phone_code')
                                    ->default('SA')
                                    ->preferredCountries(['SA', 'AE', 'EG'])
                                    ->rules(NULLABLE_NUMERIC_VALIDATION),

                                select::make('Cities', 'cities')
                                    ->options(\App\Models\City::pluck('name', 'id')),

                                Select::make('with')
                                    ->options([
                                        'individual' => 'Individual',
                                        'company' => 'Company',
                                        'both' => 'Both'
                                    ])
                                    ->default('both'),
                            ]),

                        Select::make('type')
                            ->options([
                                'factory' => 'Factory',
                                'distributor' => 'Distributor',
                                'wholesales' => 'Wholesales',
                                'retail' => 'Retail'
                            ]),

                        TextInput::make('facebook_url')->rules(NULLABLE_STRING_VALIDATION),
                        TextInput::make('twitter_url')->rules(NULLABLE_STRING_VALIDATION),
                        TextInput::make('youtube_url')->rules(NULLABLE_STRING_VALIDATION),
                        TextInput::make('instagram_url')->rules(NULLABLE_STRING_VALIDATION),
                        TextInput::make('pinterest_url')->rules(NULLABLE_STRING_VALIDATION),
                        TextInput::make('website_url')->rules(NULLABLE_STRING_VALIDATION),

                        Fields::image(true, VENDOR_COVER, 'Cover', true),
                        Fields::image(true, VENDOR_LOGO, 'Logo', true),
                        Fields::file(true, VENDOR_PATH, 'Licenses', false),


                        Toggle::make('active'),
                        Toggle::make('sold_by_youmats'),
                        Toggle::make('manage_by_admin'),
                        Toggle::make('enable_encryption_mode'),
                        Toggle::make('enable_3cx'),
                        Toggle::make('isFeatured'),

                        TextInput::make('password')
                            ->password()
                            ->revealable(),

                        Fields::SEO(static::$model, 'vendors'),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name'),

                Tables\Columns\TextColumn::make('call_phone'),
                Tables\Columns\TextColumn::make('phone'),

                Tables\Columns\ToggleColumn::make('active')->sortable(),
                Tables\Columns\ToggleColumn::make('sold_by_youmats')->sortable(),
                Tables\Columns\ToggleColumn::make('manage_by_admin')->sortable(),
                Tables\Columns\ToggleColumn::make('enable_encryption_mode')->sortable(),
                Tables\Columns\ToggleColumn::make('enable_3cx')->sortable(),
                Tables\Columns\ToggleColumn::make('isFeatured')->sortable(),

                Tables\Columns\TextColumn::make('link'),

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
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
