<?php

namespace App\Filament\Resources;

use App\Helpers\Filament\Fields;
use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationGroup = 'Orders';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        TextInput::make('quote_no'),

                        TextInput::make('name')
                            ->rules(REQUIRED_STRING_VALIDATION),

                        TextInput::make('email')
                            ->email()
                            ->rules(REQUIRED_EMAIL_VALIDATION),

                        PhoneInput::make('phone')
                            ->default('SA')
                            ->preferredCountries(['SA', 'AE', 'EG'])
                            ->rules(NULLABLE_STRING_VALIDATION),

                        PhoneInput::make('phone2')
                            ->default('SA')
                            ->preferredCountries(['SA', 'AE', 'EG'])
                            ->rules(NULLABLE_STRING_VALIDATION),

                        TextInput::make('address')
                            ->rules(REQUIRED_STRING_VALIDATION),

                        Fields::file(false, QUOTE_ATTACHMENT, 'Attachments', false),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'refused' => 'Refused'
                            ])
                            ->default('pending'),

                        TextInput::make('notes')
                            ->rules(NULLABLE_TEXT_VALIDATION),

                        TextInput::make('estimated price')
                            ->numeric()
                            ->minValue(0)
                            ->rules(NULLABLE_NUMERIC_VALIDATION),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('quote_no'),
                TextColumn::make('created_at'),
                TextColumn::make('phone'),
                Tables\Columns\SelectColumn::make('Status')->options([
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                    'refused' => 'Refused'
                ]),
                TextColumn::make('estimated price'),

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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
