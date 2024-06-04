<?php

namespace App\Helpers\Traits;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

trait DefaultImage {

    protected static string $defaultImage = 'https://youmats-media.s3.me-central-1.amazonaws.com/extra/placeholder.webp';
    protected static string $defaultCover = 'https://via.placeholder.com/1350x300/f5f5f5/003f91.webp?text=YouMats';

    public function getFirstMediaUrlOrDefault(string $collectionName = '', string $conversionName = 'cropper')
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $url = $this->getFirstMediaUrl($collectionName, $conversionName);
        $image = '';

        if($collectionName == VENDOR_COVER || $collectionName == USER_COVER)
            $image = $this::$defaultCover;
        else
            $image = $this::$defaultImage;

        $collection = $this->getFirstMedia($collectionName);

        if($collection) {
            $title = json_decode((string) $collection->img_title , true)[$locale] ?? $this->name ?? $this->title;
            $alt = json_decode((string) $collection->img_alt, true)[$locale] ?? $this->name ?? $this->title;
        } else {
            $title = $this->name;
            $alt = $this->name;
        }

        return [
            'url' => strlen($url) > 1 ? $url : $image,
            'title' => $title,
            'alt' => $alt
        ];
    }
}
