<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $mergedNameData = [];
        $mergedTempNameData = [];

        foreach ($data as $key => $translations) {

            if (str_starts_with($key, 'title_template')) {
                foreach ($translations as $lang => $text) {

                    if (!isset($mergedNameData[$lang])) {
                        $mergedNameData[$lang] = [];
                    }
                    $mergedNameData[$lang][$key] = $text;
                }
            }
        }

        foreach ($mergedNameData as $lang => $entries) {

            uksort($entries, function ($a, $b) {
                return strnatcmp($a, $b);
            });

            $mergedNameData[$lang] = implode(" ", $entries);
            $mergedTempNameData[$lang] = implode("^", $entries);
        }

        $jsonNameData = json_encode($mergedNameData);
        $jsonTempNameData = json_encode($mergedTempNameData);

        $data['name'] = $jsonNameData;
        $data['temp_name'] = $jsonTempNameData;

        return $data;
    }
}
