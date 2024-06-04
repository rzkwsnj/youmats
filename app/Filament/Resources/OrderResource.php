<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Orders';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        TextInput::make('order_id'),

                        Select::make('user_id')
                            ->relationship('user', 'name'),

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
                            ->rules(REQUIRED_STRING_VALIDATION),

                        TextInput::make('address')
                            ->rules(REQUIRED_STRING_VALIDATION),

                        TextInput::make('building_number')
                            ->rules(NULLABLE_STRING_VALIDATION),

                        TextInput::make('street')
                            ->rules(NULLABLE_STRING_VALIDATION),

                        TextInput::make('district')
                            ->rules(NULLABLE_STRING_VALIDATION),

                        TextInput::make('city')
                            ->rules(NULLABLE_STRING_VALIDATION),

                        Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'credit card' => 'Credit Card',
                                'paypal' => 'PayPal'
                            ]),

                        /*
                DependencyContainer::make([
                    Text::make('Reference Number')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                    Text::make('Card Number')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                    Text::make('Card Type')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                    Text::make('Card Name')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                    Text::make('Card Expire Date', 'card_exp_date')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                    DateTime::make('Transaction Date')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                ])->dependsOnNot('payment_method', 'cash'),

                    */

                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'refunded' => 'Refunded',
                                'completed' => 'Completed'
                            ]),

                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'shipping' => 'Shipping',
                                'completed' => 'Completed',
                                'refused' => 'Refused'
                            ]),

                        TextInput::make('notes')
                            ->rules(NULLABLE_TEXT_VALIDATION),

                        TextInput::make('refused_notes')
                            ->rules(NULLABLE_TEXT_VALIDATION),

                        TextInput::make('subtotal')
                            ->numeric()
                            ->rules(REQUIRED_NUMERIC_VALIDATION),

                        TextInput::make('delivery')
                            ->numeric()
                            ->rules(REQUIRED_NUMERIC_VALIDATION),

                        TextInput::make('total_price')
                            ->numeric()
                            ->rules(REQUIRED_NUMERIC_VALIDATION),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('order_id'),
                TextColumn::make('created_at')->sortable(),
                TextColumn::make('name'),
                TextColumn::make('phone'),
                TextColumn::make('payment_method'),
                TextColumn::make('payment_status'),
                TextColumn::make('status'),
                TextColumn::make('total_price'),

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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
