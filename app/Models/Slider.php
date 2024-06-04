<?php

namespace App\Models;

use App\Helpers\Traits\DefaultImage;
use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Slider extends Model implements Sortable, HasMedia
{
    use SoftDeletes, HasFactory, SortableTrait, HasTranslations, InteractsWithMedia, DefaultImage, UnicodeJsonColumn;

    public $fillable = ['quote', 'title', 'button_title', 'button_link'];

    public $translatable = ['quote', 'title', 'button_title'];

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('size_height_270')
            ->height(270)
            ->nonQueued()
            ->performOnCollections(SLIDER_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_400_270')
            ->crop(Manipulations::CROP_CENTER, 400, 270)
            ->nonQueued()
            ->performOnCollections(SLIDER_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(SLIDER_PATH)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection(SLIDER_PATH)->singleFile();
    }

}
