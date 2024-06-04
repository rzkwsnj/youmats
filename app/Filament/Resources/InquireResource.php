<?php

namespace App\Filament\Resources;

use App\Helpers\Filament\Fields;
use App\Filament\Resources\InquireResource\Pages;
use App\Models\Inquire;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class InquireResource extends Resource
{
    protected static ?string $model = Inquire::class;

    protected static ?string $navigationGroup = 'Users';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        TextInput::make('company_name')
                            ->rules(REQUIRED_STRING_VALIDATION),

                        TextInput::make('name')
                            ->rules(REQUIRED_STRING_VALIDATION),

                        TextInput::make('email')
                            ->email()
                            ->rules(REQUIRED_EMAIL_VALIDATION),

                        PhoneInput::make('phone')
                            ->default('SA')
                            ->preferredCountries(['SA', 'AE', 'EG'])
                            ->rules(REQUIRED_STRING_VALIDATION),

                        Textarea::make('message')
                            ->rules(NULLABLE_TEXT_VALIDATION),

                        Fields::file(false, INQUIRE_PATH, 'File', true)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('company_name'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('created_at'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone'),

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
            'index' => Pages\ListInquires::route('/'),
            'create' => Pages\CreateInquire::route('/create'),
            'edit' => Pages\EditInquire::route('/{record}/edit'),
        ];
    }
}
