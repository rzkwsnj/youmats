<?php

namespace App\Filament\Resources\NovaSettingsResource\Pages;

use App\Filament\Resources\NovaSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNovaSettings extends ListRecords
{
    protected static string $resource = NovaSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
