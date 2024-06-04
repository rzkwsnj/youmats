<?php

namespace App\Helpers\Filament;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;

class Fields
{

    public static function SEO(string $model, string $tableName, bool $requiredSlug = true, bool $is_canonical = false, bool $translatable_schema = false)
    {
        if ($requiredSlug)
            $slugValidation = REQUIRED_STRING_VALIDATION;
        else
            $slugValidation = NULLABLE_STRING_VALIDATION;


        return Section::make('SEO')
            ->description('SEO section')
            ->schema([
                TextInput::make('slug')
                    ->rules($slugValidation),

                TextInput::make('meta_title')
                    ->rules(NULLABLE_STRING_VALIDATION)
                    ->translatable(),

                TextInput::make('meta_keywords')
                    ->rules(NULLABLE_TEXT_VALIDATION)
                    ->translatable(),

                Textarea::make('meta_desc')
                    ->rules(NULLABLE_TEXT_VALIDATION)
                    ->translatable(),

                CodeEditor::make('schema')
                    ->rules(NULLABLE_TEXT_VALIDATION)
                    ->translatable(),

                Textarea::make('canonical')
                    ->rules(NULLABLE_STRING_VALIDATION)
                    ->translatable()
                    ->visible($is_canonical)
            ]);
    }



    public static function image(bool $isRequired, string $mediaCollection, string $name = 'Image', bool $single = true, string $helpText = null)
    {
        $image = SpatieMediaLibraryFileUpload::make($name, $mediaCollection)
            ->collection($mediaCollection)
            ->preserveFilenames()
            ->image()
            ->imageEditor()
            ->openable();

        return $image;
    }


    public static function file(bool $isRequired, string $mediaCollection, string $name = 'File', bool $single = true, string $helpText = null)
    {

        $file = SpatieMediaLibraryFileUpload::make($name, $mediaCollection)
            ->collection($mediaCollection)
            ->preserveFilenames()
            ->openable();

        return $file;
    }

    public static function Stores()
    {
        return
            Section::make('stores')
            ->schema([
                Repeater::make('stores')
                    ->schema([
                        Select::make('store')
                            ->options(\App\Models\Store::pluck('name', 'id'))
                            ->rules(REQUIRED_INTEGER_VALIDATION)
                            ->translatable(),

                        TextInput::make('store_moq')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder(1)
                            ->rules(NULLABLE_INTEGER_VALIDATION),

                        TextInput::make('store_price')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder(1)
                            ->rules(NULLABLE_INTEGER_VALIDATION),

                    ])->columns(3),

                Toggle::make('store_enable')
            ]);
    }
}
