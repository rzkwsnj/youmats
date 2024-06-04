<?php

namespace App\Filament\Resources;

use App\Helpers\Filament\Fields;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Users';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        TextInput::make('name')
                            ->rules(REQUIRED_STRING_VALIDATION),

                        TextInput::make('email')
                            ->email()
                            ->rules(REQUIRED_EMAIL_VALIDATION),

                        Fields::image(false, USER_PROFILE, 'Profile', true),

                        Fields::image(false, USER_COVER, 'Cover', true),

                        PhoneInput::make('phone')
                            ->default('SA')
                            ->preferredCountries(['SA', 'AE', 'EG'])
                            ->rules(NULLABLE_STRING_VALIDATION),

                        PhoneInput::make('phone2')
                            ->default('SA')
                            ->preferredCountries(['SA', 'AE', 'EG'])
                            ->rules(NULLABLE_STRING_VALIDATION),

                        Textarea::make('address')
                            ->rules(NULLABLE_STRING_VALIDATION),

                        Textarea::make('address2')
                            ->rules(NULLABLE_STRING_VALIDATION),

                        Toggle::make('active'),

                        TextInput::make('password')
                            ->password()
                            ->revealable(),

                        Select::make('type')
                            ->options([
                                'individual' => 'Individual',
                                'company' => 'Company'
                            ]),

                        Fields::file(true, COMPANY_PATH, 'Licenses', false),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\ToggleColumn::make('active'),
                Tables\Columns\SelectColumn::make('type')->options([
                    'individual' => 'Individual',
                    'company' => 'Company'
                ])

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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
