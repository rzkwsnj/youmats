<?php


namespace App\Helpers\Classes;

use App\Models\Product;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Action;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Trait HandleGenerateProducts
{
    private $result_ar;
    private $result_en;
    private $locales;

    public function __construct()
    {
        $this->locales = LaravelLocalization::getSupportedLanguagesKeys();
    }

    private function handleModels($model, $test_mode = false) {
        $sentences = [
            'name' => (new GenerateSentence())->printf($this->reformat($model->template)),
            'desc' => (new GenerateSentence())->printf($this->reformat($this->handleDesc($model, 'desc')), true),
            'short_desc' => (new GenerateSentence())->printf($this->reformat($this->handleDesc($model, 'short_desc')), true)
        ];

        $arr = $this->replaceRealValues($model, $this->combine($sentences));

        if($test_mode) {
            dd(count($arr), array_slice($arr, 0, 10));
        }

        foreach ($arr as $row) {

            $product = Product::create([
                'category_id' => $model->category_id,
                'vendor_id' => $model->vendor_id,
                'name' => $row['name'],
                'desc' => $row['desc'],
                'short_desc' => $row['short_desc'],
                'rate' => 5,
                'type' => 'product',
                'price' => 0,
                'cost' => 0,
                'stock' => 1000,
                'SKU' => Str::sku('yt', '-'),
                'search_keywords' => $model->search_keywords,
                'active' => 0,
                'slug' => Str::slug($row['name']['en'], '-') . rand(100, 999),
                'sort' => 0
            ]);

            try {
                //Add images to the product
                if(count($model->getMedia(GENERATE_PRODUCT_PATH))) {
                    foreach($model->getMedia(GENERATE_PRODUCT_PATH) as $image) {
                        $product->addMediaFromUrl($image->getUrl())->toMediaCollection(PRODUCT_PATH);
                    }
                }
            } catch (\Exception $e) {}

        }
    }

    private function reformat($data) {
        $formattedData = [];
        $values = [];
        $maxLength = 1;

        foreach ($this->locales as $locale) {
            foreach ($data[$locale] as $key => $item) {
                $tempValue = explode('-', $item['value']);
                if($maxLength < count($tempValue)) {
                    $maxLength = count($tempValue);
                }
                $values[$locale][$key]['order'] = $item['order'];
                $values[$locale][$key]['value'] = $tempValue;
            }
        }

        foreach ($this->locales as $locale) {
            foreach ($values[$locale] as $key => $row) {
                if(!empty($row['value'][0])) {
                    $formattedData[$locale][$key]['order'] = $row['order'];
                    if(count($row['value']) < $maxLength) {
                        $formattedData[$locale][$key]['value'] = $row['value'];
                        for ($i = count($row['value']); $i < $maxLength; $i++) {
                            $formattedData[$locale][$key]['value'][$i] = '';
                        }
                    } else {
                        $formattedData[$locale][$key]['value'] = $row['value'];
                    }
                }
            }
        }

        return [
            'data' => $formattedData,
            'maxLength' => $maxLength
        ];
    }

    private function combine($arr) {
        $result = [];
        for ($i = 0; $i < count($arr['name']['ar']); $i++) {
            $result[$i]['name']['ar'] = $arr['name']['ar'][$i];
            $result[$i]['name']['en'] = $arr['name']['en'][$i];
            $result[$i]['desc']['ar'] = $arr['desc']['ar'][$i];
            $result[$i]['desc']['en'] = $arr['desc']['en'][$i];
            $result[$i]['short_desc']['ar'] = $arr['short_desc']['ar'][$i];
            $result[$i]['short_desc']['en'] = $arr['short_desc']['en'][$i];
        }
        return $result;
    }

    private function replaceRealValues($model, $arr) {
        $orderedValues = $this->getOrderedValues($model->template, true);
        $result = [];
        foreach ($arr as $key => $row) {
            $result[$key] = $this->replaceValue($row, $orderedValues);
        }
        return $result;
    }

    private function replaceValue($row, $values) {
        $indexes = [];
        $newSentence = [];
        foreach ($row as $itemName => $item) {
            foreach ($item as $locale => $value) {
                $sentence = explode('#', $value);
                $newSentence[$itemName][$locale] = '';
                foreach ($sentence as $word) {
                    if ($locale == 'ar' && $itemName == 'name' && preg_match('/^[0-9.]+$/', $word)) {
                        $tempIndex = explode('.', $word);
                        $indexes[$tempIndex[0]] = $tempIndex[1];
                        $newSentence[$itemName][$locale] .= $values[$locale][$tempIndex[0]][$tempIndex[1]];
                    } elseif ($locale != 'ar' && preg_match('/[0-9]$/', $word)) {
                        $originalIndex = $word;
                        $newSentence[$itemName][$locale] .= $values[$locale][$originalIndex][$indexes[$originalIndex]];
                    } elseif ($locale == 'ar' && $itemName != 'name' && preg_match('/^[0-9.]+$/', $word)) {
                        $originalIndex = explode('.', $word)[0];
                        $newSentence[$itemName][$locale] .= $values[$locale][$originalIndex][$indexes[$originalIndex]];
                    } else {
                        $newSentence[$itemName][$locale] .= $word;
                    }
                }
            }
        }
        return $newSentence;
    }

    private function getOrderedValues($template, $array = false) {
        $result = [];
        foreach ($this->locales as $locale) {
            foreach ($template[$locale] as $row) {
                if($row['order']) {
                    if($array)
                        $result[$locale][$row['order']] = explode('-', $row['value']);
                    else
                        $result[$locale][$row['order']] = $row['value'];
                }
            }
        }
        return $result;
    }

    private function handleDesc($model, $attr) {
        $orderedValues = $this->getOrderedValues($model->template);
        $result = [];
        foreach ($this->locales as $locale) {
            foreach (explode('##', strip_tags($model->getTranslation($attr, $locale))) as $key => $row) {
                if(is_numeric($row)) {
                    $result[$locale][$key]['order'] = $row;
                    $result[$locale][$key]['value'] = $orderedValues[$locale][$row];
                } elseif(!empty($row)) {
                    $result[$locale][$key]['order'] = '';
                    $result[$locale][$key]['value'] = $row;
                }
            }
            if(array_key_first($result[$locale]) != 0) {
                $temp = [];
                foreach ($result[$locale] as $key => $item) {
                    $temp[$key - array_key_first($result[$locale])] = $item;
                }
                $result[$locale] = $temp;
            }
        }
        return $result;
    }
}
