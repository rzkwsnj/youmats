<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TripResource\Pages;
use App\Models\Trip;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use IbrahimBougaoua\FilamentRatingStar\Actions\RatingStar;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static ?string $navigationGroup = 'Tracker';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        Select::make('user')
                            ->relationship('user', 'name'),

                        Select::make('driver')
                            ->relationship('driver', 'name'),

                        TextInput::make('distance')
                            ->numeric()
                            ->rules(REQUIRED_NUMERIC_VALIDATION)
                            ->minValue(0)
                            ->step(0.01),

                        TextInput::make('price')
                            ->rules(NULLABLE_NUMERIC_VALIDATION)
                            ->minValue(0)
                            ->step(0.05),

                        Select::make('Driver Status')
                            ->options([
                                '0' => 'Pending',
                                '1' => 'Accepted'
                            ]),

                        Select::make('Status')
                            ->options([
                                '0' => 'Pending',
                                '1' => 'In progress',
                                '2' => 'Completed',
                                '3' => 'Canceled'
                            ])
                            ->rules(array_merge(REQUIRED_STRING_VALIDATION, ['In:0,1,2,3'])),

                        DateTimePicker::make('pickup date'),

                        DateTimePicker::make('started at'),

                        RatingStar::make('user rate')
                            ->rules(NULLABLE_NUMERIC_VALIDATION),

                        TextInput::make('user review'),

                        RatingStar::make('driver rate')
                            ->rules(NULLABLE_NUMERIC_VALIDATION),

                        TextInput::make('driver review'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user'),
                TextColumn::make('driver'),
                TextColumn::make('distance'),
                TextColumn::make('Driver Status'),
                TextColumn::make('Status'),
                TextColumn::make('pickup date'),
                TextColumn::make('started at'),

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
            'index' => Pages\ListTrips::route('/'),
            'create' => Pages\CreateTrip::route('/create'),
            'edit' => Pages\EditTrip::route('/{record}/edit'),
        ];
    }
}
