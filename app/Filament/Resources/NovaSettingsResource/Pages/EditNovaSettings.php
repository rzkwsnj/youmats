<?php

namespace App\Filament\Resources\NovaSettingsResource\Pages;

use App\Filament\Resources\NovaSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNovaSettings extends EditRecord
{
    protected static string $resource = NovaSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
