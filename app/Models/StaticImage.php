<?php

namespace App\Models;

use App\Helpers\Traits\DefaultImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StaticImage extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, DefaultImage;

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('size_height_45')
            ->height(45)
            ->nonQueued()
            ->performOnCollections(LOGO_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_25')
            ->height(25)
            ->nonQueued()
            ->performOnCollections(LOGO_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_16_16')
            ->crop(Manipulations::CROP_CENTER, 16, 16)
            ->nonQueued()
            ->performOnCollections(FAVICON_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_32_32')
            ->crop(Manipulations::CROP_CENTER, 32, 32)
            ->nonQueued()
            ->performOnCollections(FAVICON_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_438')
            ->height(438)
            ->nonQueued()
            ->performOnCollections(SLIDER_BACKGROUND_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_1920_438')
            ->crop(Manipulations::CROP_CENTER, 1920, 438)
            ->nonQueued()
            ->performOnCollections(SLIDER_BACKGROUND_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_100')
            ->height(100)
            ->nonQueued()
            ->performOnCollections(HOME_FIRST_SECTION_PATH, HOME_SECOND_SECTION_PATH, HOME_THIRD_SECTION_PATH)
            ->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_144_100')
            ->crop(Manipulations::CROP_CENTER, 144, 100)
            ->nonQueued()
            ->performOnCollections(HOME_FIRST_SECTION_PATH, HOME_SECOND_SECTION_PATH, HOME_THIRD_SECTION_PATH)
            ->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(LOGO_PATH, FAVICON_PATH, SLIDER_BACKGROUND_PATH, HOME_FIRST_SECTION_PATH, HOME_SECOND_SECTION_PATH, HOME_THIRD_SECTION_PATH)
            ->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection(LOGO_PATH)->singleFile();
        $this->addMediaCollection(FAVICON_PATH)->singleFile();
        $this->addMediaCollection(SLIDER_BACKGROUND_PATH)->singleFile();
        $this->addMediaCollection(HOME_FIRST_SECTION_PATH)->singleFile();
        $this->addMediaCollection(HOME_SECOND_SECTION_PATH)->singleFile();
        $this->addMediaCollection(HOME_THIRD_SECTION_PATH)->singleFile();
    }
}
