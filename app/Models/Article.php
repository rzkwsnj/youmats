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

class Article extends Model implements Sortable, HasMedia
{
    use SoftDeletes, HasFactory, SortableTrait, HasTranslations, InteractsWithMedia, DefaultImage, UnicodeJsonColumn;

    protected $guarded = ['id'];

    public $translatable = ['name', 'desc', 'short_desc', 'meta_title', 'meta_keywords', 'meta_desc'];

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('size_height_500')
            ->height(50)
            ->nonQueued()
            ->performOnCollections(ARTICLE_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(ARTICLE_PATH)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection(ARTICLE_PATH);
    }

    public function tags() {
        return $this->belongsToMany(Tag::class);
    }

    public function vendors() {
        return $this->belongsToMany(Vendor::class);
    }

    public function categories() {
        return $this->belongsToMany(Category::class);
    }
}
