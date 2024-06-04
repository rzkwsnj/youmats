<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Helpers\Filament\Fields;
use App\Models\Currency;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationGroup = 'General-Settings';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        TextInput::make('name')
                            ->rules(REQUIRED_STRING_VALIDATION)
                            ->required(),

                        Fields::image(false, CURRENCY_PATH, 'Image', true),

                        TextInput::make('code')
                            ->rules(REQUIRED_STRING_VALIDATION)
                            ->required(),

                        TextInput::make('symbol')
                            ->rules(REQUIRED_STRING_VALIDATION)
                            ->translatable(),

                        TextInput::make('rate')
                            ->numeric()
                            ->step(0.000001)
                            ->rules(NULLABLE_NUMERIC_VALIDATION),

                        Toggle::make('active'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name'),
                TextColumn::make('code'),
                TextColumn::make('symbol'),
                TextColumn::make('rate')->sortable(),
                Tables\Columns\ToggleColumn::make('active')->sortable(),
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
