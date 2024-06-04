<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeValueResource\Pages;
use App\Models\AttributeValue;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Guava\FilamentNestedResources\Ancestor;

class AttributeValueResource extends Resource
{
    use NestedResource;

    protected static ?string $model = AttributeValue::class;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        Select::make('attribute')
                            ->relationship('attribute', 'key'),

                        TextInput::make('value')
                            ->rules(REQUIRED_STRING_VALIDATION)
                            ->translatable(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('value'),
                Tables\Columns\TextColumn::make('attribute.key'),

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
            'index' => Pages\ListAttributeValues::route('/'),
            'create' => Pages\CreateAttributeValue::route('/create'),
            'edit' => Pages\EditAttributeValue::route('/{record}/edit'),

        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        return Ancestor::make('values', 'attribute');
    }
}
