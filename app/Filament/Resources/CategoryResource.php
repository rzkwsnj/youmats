<?php

namespace App\Filament\Resources;

use App\Helpers\Filament\Fields;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationGroup = 'Products';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        TextInput::make('name')
                            ->rules(REQUIRED_STRING_VALIDATION)
                            ->translatable(),

                        TextInput::make('title')
                            ->rules(NULLABLE_STRING_VALIDATION)
                            ->translatable(),

                        Forms\Components\Checkbox::make('category'),

                        Forms\Components\Select::make('parent')
                            ->relationship('parent', 'name'),

                        Forms\Components\Textarea::make('desc')
                            ->rules(NULLABLE_TEXT_VALIDATION)
                            ->translatable(),

                        Fields::image(true, CATEGORY_PATH, 'Image', true),

                        Fields::image(true, CATEGORY_COVER, 'Cover', true),

                        TextInput::make('featured_section_order')
                            ->numeric(),

                        Toggle::make('featured_sections'),
                        Toggle::make('isFeatured'),
                        Toggle::make('topCategory'),
                        Toggle::make('show_in_footer'),
                        Toggle::make('hide_availability'),
                        Toggle::make('hide_delivery_status'),
                        Toggle::make('contact_widgets'),
                        Toggle::make('show_warning'),
                    ]),

                Section::make('Template For Title')
                    ->description('Instructions: + => for input, - => for dropdown, Ex for dropdown: -Orientation-Horizontal-Vertical')
                    ->schema([
                        Repeater::make('template')
                            ->schema([
                                TextInput::make('word')
                                    ->rules(NULLABLE_TEXT_VALIDATION)
                                    ->translatable(),
                            ])
                    ]),

                Fields::SEO(static::$model, 'categories', true, false, true),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('parent.name'),
                Tables\Columns\ToggleColumn::make('featured_sections')->sortable(),
                Tables\Columns\ToggleColumn::make('isFeatured')->sortable(),
                Tables\Columns\ToggleColumn::make('topCategory')->sortable(),
                Tables\Columns\ToggleColumn::make('show_in_footer')->sortable(),

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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
