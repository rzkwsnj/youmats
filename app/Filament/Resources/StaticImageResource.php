<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaticImageResource\Pages;
use App\Helpers\Filament\Fields;
use App\Models\StaticImage;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StaticImageResource extends Resource
{
    protected static ?string $model = StaticImage::class;

    protected static ?string $navigationGroup = 'Management';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([
                        Fields::image(true, LOGO_PATH, 'Logo', true),
                        Fields::image(true, FAVICON_PATH, 'Favicon', true),
                        Fields::image(true, SLIDER_BACKGROUND_PATH, 'Slider Background', true),
                        Fields::image(true, HOME_FIRST_SECTION_PATH, 'Home First Section', true),
                        Fields::image(true, HOME_SECOND_SECTION_PATH, 'Home Second Section', true),
                        Fields::image(true, HOME_THIRD_SECTION_PATH, 'Home Third Section', true),
                        Fields::image(true, WHATSAPP_QR_CODE_PATH, 'Whatsapp QR code', true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                SpatieMediaLibraryImageColumn::make('Logo'),
                SpatieMediaLibraryImageColumn::make('Favicon'),
                SpatieMediaLibraryImageColumn::make('Slider Background'),
                SpatieMediaLibraryImageColumn::make('Home First Section'),
                SpatieMediaLibraryImageColumn::make('Home Second Section'),
                SpatieMediaLibraryImageColumn::make('Home Third Section'),
                SpatieMediaLibraryImageColumn::make('Whatsapp QR code'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
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
            'index' => Pages\ListStaticImages::route('/'),
            'create' => Pages\CreateStaticImage::route('/create'),
            'edit' => Pages\EditStaticImage::route('/{record}/edit'),
        ];
    }
}
