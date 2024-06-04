<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class GenerateProduct extends Model implements HasMedia
{
    use HasFactory, UnicodeJsonColumn, HasTranslations, InteractsWithMedia;

    protected $guarded = ['id'];

    public $translatable = ['short_desc', 'desc', 'search_keywords'];

    protected $casts = [
        'template' => 'json'
    ];

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(GENERATE_PRODUCT_PATH);
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection(GENERATE_PRODUCT_PATH);
    }

    /**
     * @return BelongsTo
     */
    public function category() {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function vendor() {
        return $this->belongsTo(Vendor::class);
    }
}
