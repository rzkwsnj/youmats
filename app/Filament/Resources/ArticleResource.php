<?php

namespace App\Filament\Resources;

use App\Helpers\Filament\Fields;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;


class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationGroup = 'Blog';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        TextInput::make('name')
                            ->rules(REQUIRED_STRING_VALIDATION)
                            ->translatable(),

                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple(),

                        Select::make('vendors')
                            ->relationship('vendors', 'name')
                            ->multiple(),

                        Select::make('categories')
                            ->relationship('categories', 'name')
                            ->multiple(),

                        Textarea::make('short_desc')
                            ->rules(NULLABLE_TEXT_VALIDATION)
                            ->translatable(),

                        Textarea::make('desc')
                            ->rules(REQUIRED_TEXT_VALIDATION)
                            ->translatable(),

                        Fields::image(true, ARTICLE_PATH, 'Image', true),

                        Toggle::make('active'),
                    ]),
                Fields::SEO(static::$model, 'articles'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name'),
                ToggleColumn::make('active'),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
