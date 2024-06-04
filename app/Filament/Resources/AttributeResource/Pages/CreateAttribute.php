<?php

namespace App\Filament\Resources\AttributeResource\Pages;

use App\Filament\Resources\AttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateAttribute extends CreateRecord
{
    use NestedPage;

    protected static string $resource = AttributeResource::class;

    protected static string $relationship = 'values';
}
