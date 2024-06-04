<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Helpers\Filament\Fields;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use IbrahimBougaoua\FilamentRatingStar\Actions\RatingStar;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        Select::make('category')
                            ->reactive()
                            ->relationship('category', 'name->' . LaravelLocalization::getCurrentLocale())
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('template_ref_id', intval($state));
                            })
                            ->required(),

                        Select::make('vendor')
                            ->relationship('vendor', 'name->' . LaravelLocalization::getCurrentLocale())
                            ->required(),

                        Section::make('Title Template')
                            ->label('Name')
                            ->description('Instructions: + => for input, - => for dropdown, Ex for dropdown: -Orientation-Horizontal-Vertical')
//                            ->hidden(fn(Page $livewire, Get $get): bool => $livewire instanceof Pages\EditProduct ? empty($get('template_ref_id')) : empty($get('category')))
                            ->hidden(fn(Get $get): bool => empty($get('category')))
//                            ->visible(fn(Page $livewire, Get $get) => dd($get('category')))
                            ->schema([
                                Group::make(fn(Get $get, Page $livewire): array => self::getTitleTemplateSchema($get, $livewire))
                                    ->columns(3)
                            ]),

                        Hidden::make('template_ref_id')
                            ->reactive()
                            ->dehydrated(false)
                            ->default(false),

                        Hidden::make('temp_name'),
                        Hidden::make('name'),

                        // TODO: translatable() not work in multiple select !
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple(),

                        Textarea::make('short_desc')
                            ->rules(NULLABLE_TEXT_VALIDATION)
                            ->translatable(),

                        Textarea::make('desc')
                            ->rules(REQUIRED_TEXT_VALIDATION)
                            ->translatable(),

                        Select::make('type')->options([
                            'product' => 'Product',
                            'service' => 'Service'
                        ]),

                        Select::make('unit')
                            ->relationship('unit', 'name->' . LaravelLocalization::getCurrentLocale()),

                        TextInput::make('youmats_moq')
                            ->minValue(0)
                            ->default(2)
                            ->rules(REQUIRED_INTEGER_VALIDATION),

                        TextInput::make('min_quantity')
                            ->minValue(2)
                            ->rules(REQUIRED_INTEGER_VALIDATION),

                        TextInput::make('sku')
                            ->rules(NULLABLE_STRING_VALIDATION)
                            ->default(Str::sku('yt', '-')),

                        RatingStar::make('rate'),

                        Toggle::make('active'),

                        Toggle::make('best_seller'),

                        Select::make('attributes')
                            ->relationship('attributes', 'value'),

                        Fields::image(false, PRODUCT_PATH, 'images', false)

                    ]),

                Fields::Stores(),

                Section::make('Search Keywords')
                    ->description('Instructions: Set every keyword in one line')
                    ->schema([
                        Textarea::make('Search Keywords')
                            ->rules(NULLABLE_TEXT_VALIDATION)
                            ->translatable()
                    ]),


                Fields::SEO(static::$model, 'products', false, true),

            ]);
    }

    public static function getTitleTemplateSchema(Get $get, Page $livewire): array
    {
        $schema = [];

        $cat = Category::find(intval($get('category')));

        if (!is_null($cat) && !is_null($cat->template)) {

            foreach ($cat->template as $k => $v) {

                if (Str::contains($v['word']['en'], '---')) {
                    $clear = str_replace('---', '', $v['word']['en']);
                    $arr = explode('-', $clear);
                    $options = array_combine($arr, $arr);
                    $schema[] = Select::make('title_template_' . $k + 1)
                        ->hiddenLabel()
                        ->options($options)
                        ->required()
                        ->translatable();

                } else if (Str::contains($v['word']['en'], '+')) {
                    $schema[] = TextInput::make('title_template_' . $k + 1)
                        ->placeholder(str_replace(str_split('+'), '', $v['word']['en']))
                        ->required()
                        ->hiddenLabel()
                        ->translatable();

                } else {

                    $schema[] = Select::make('title_template_' . $k + 1)
                        ->hiddenLabel()
                        ->options(array_combine([$v['word']['en']], [$v['word']['en']]))
                        ->required()
                        ->translatable();

                }
            }

        } else {
            $schema[] = TextInput::make('title_template_0')
                ->placeholder('Please input product name')
                ->required()
                ->hiddenLabel();
        }

        return $schema;

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(function (string $state) {
                        return json_decode($state, true)[LaravelLocalization::getCurrentLocale()];
                    }),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('vendor.name'),
                Tables\Columns\ToggleColumn::make('active'),
                Tables\Columns\TextColumn::make('link')

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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
