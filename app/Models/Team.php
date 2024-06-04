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

class Team extends Model implements Sortable, HasMedia
{
    use SoftDeletes, HasFactory, SortableTrait, HasTranslations, InteractsWithMedia, DefaultImage, UnicodeJsonColumn;

    public $translatable = ['name', 'position', 'info'];

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('size_height_120')
            ->height(120)
            ->nonQueued()
            ->performOnCollections(TEAM_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_120_120')
            ->crop(Manipulations::CROP_CENTER, 120, 120)
            ->nonQueued()
            ->performOnCollections(TEAM_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(TEAM_PATH)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection(TEAM_PATH)->singleFile();
    }
}
