<?php

namespace App\Filament\Resources\StaticImageResource\Pages;

use App\Filament\Resources\StaticImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaticImages extends ListRecords
{
    protected static string $resource = StaticImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
