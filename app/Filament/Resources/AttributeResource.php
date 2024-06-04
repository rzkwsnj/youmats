<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeResource\Pages;
use App\Models\Attribute;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Guava\FilamentNestedResources\Ancestor;

class AttributeResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Attribute::class;

    protected static ?string $navigationGroup = 'General-Settings';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        Select::make('category_id')
                            ->relationship('Category', 'name'),

                        TextInput::make('key')
                            ->rules(REQUIRED_STRING_VALIDATION)
                            ->translatable(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('key', 'name'),

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),

            'values.create' => Pages\ManageAttributeValues::route('/{record}/attribute-values/create'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditAttribute::class,
            Pages\ManageAttributeValues::class,
        ]);
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }
}
