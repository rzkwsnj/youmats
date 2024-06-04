<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        $templateRaw = Category::find($data['category_id'])->template;
        // dd(count($templateRaw));

        $tempName = json_decode($data['temp_name'], true);
        $resultTempName = [];

        foreach ($tempName as $lang => $text) {
            $resultTempName[$lang] = explode('^', $text);
        }

        // dd($resultTempName['en'][0]);

        foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
            // dd(count($resultTempName[$localeCode]));

            if (count($resultTempName[$localeCode]) === count($templateRaw)) {

                foreach ($resultTempName[$localeCode] as $k => $v) {
                    $data['title_template_' . $k + 1][$localeCode] = $resultTempName[$localeCode][$k];
                }

            }
        }

        // dd($data);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
