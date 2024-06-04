<?php

namespace App\Models;

use App\Helpers\Traits\DefaultImage;
use App\Helpers\Traits\UnicodeJsonColumn;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Category extends Model implements Sortable, HasMedia
{
    use SoftDeletes, HasFactory, SortableTrait, HasTranslations, InteractsWithMedia, DefaultImage, CascadeSoftDeletes, UnicodeJsonColumn;

    use NodeTrait {
        ancestors as traitAncestors;
    }

    public $fillable = ['name', 'title', 'desc', 'meta_title', 'meta_keywords', 'meta_desc', 'schema'];
    public $translatable = ['name', 'title', 'desc', 'meta_title', 'meta_keywords', 'meta_desc', 'schema'];

    protected $casts = [
        'template' => 'array',
        'deleted_at' => 'datetime'
    ];

    protected $cascadeDeletes = ['children', 'allProducts', 'products'];

    public function getNameAttribute()
    {
        if (!isset($this->getTranslations('name')[app()->getLocale()]))
            return;
        return $this->getTranslations('name')[app()->getLocale()];
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('size_height_85')
            ->height(85)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_85_85')
            ->crop(Manipulations::CROP_CENTER, 85, 85)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_570')
            ->height(570)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_305_570')
            ->crop(Manipulations::CROP_CENTER, 305, 570)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_150')
            ->height(150)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_100_100')
            ->crop(Manipulations::CROP_CENTER, 100, 100)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_150_150')
            ->crop(Manipulations::CROP_CENTER, 150, 150)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_300')
            ->height(300)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_200_300')
            ->crop(Manipulations::CROP_CENTER, 200, 300)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_width_290')
            ->width(290)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_width_350')
            ->width(350)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_350_350')
            ->crop(Manipulations::CROP_CENTER, 350, 350)
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_364')
            ->height(364)
            ->nonQueued()
            ->performOnCollections(CATEGORY_COVER)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_255_364')
            ->crop(Manipulations::CROP_CENTER, 255, 364)
            ->nonQueued()
            ->performOnCollections(CATEGORY_COVER)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(CATEGORY_PATH, CATEGORY_COVER)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(CATEGORY_PATH)->singleFile();
        $this->addMediaCollection(CATEGORY_COVER)->singleFile();
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function ancestors()
    {
        return $this->traitAncestors()->defaultOrder();
    }

    /**
     * @return HasMany
     */
    public function allProducts()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return HasMany|HasManyThrough
     */
    public function products()
    {
        if ($this->isRoot()) {
            return $this->hasManyThrough(Product::class, self::class, 'parent_id')
                ->where('products.active', true)
                ->orderBy('products.updated_at', 'desc');
        }
        if ($this->isLeaf()) {
            return $this->hasMany(Product::class)
                ->where('products.active', true)
                ->orderBy('products.updated_at', 'desc');
        }
        return Product::with([
            'media',
            'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug')
        ])->whereHas(
            'category',
            fn ($query) =>
            $query->whereDescendantOrSelf($this)
        )->where('products.active', true)->orderBy('products.updated_at', 'desc');
    }

    /**
     * To get products in front
     * @param $limit
     * @return Builder|Collection
     */
    public function frontProducts($limit)
    {
        return Product::with([
            'media',
            'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug')
        ])->whereHas('category', fn ($q) => $q->whereDescendantOrSelf($this))
            ->where('products.active', true)->orderBy('products.updated_at', 'desc')
            ->select('products.id', 'products.category_id', 'products.name', 'products.slug', 'products.type', 'products.price')
            ->limit($limit)->get();
    }

    /**
     * @return Builder
     */
    public function vendors(): Builder
    {
        return $this->belongsToMany(Vendor::class, Product::class)
            ->join('categories', 'categories.id', 'products.category_id')
            ->orWhere('categories.parent_id', $this->id)
            ->distinct()->get()->unique()->toQuery();
    }

    /**
     * @return mixed
     */
    public function subscribedVendors()
    {
        $ancestorIds = $this->getRelation('ancestors')->pluck('id')->push($this->id);

        $subscribes = Subscribe::where(function ($query) use ($ancestorIds) {
            foreach ($ancestorIds as $ancestorId) {
                $query->orWhere('category_id', $ancestorId);
            }
        })->whereDate('expiry_date', '>', now())->distinct()->pluck('vendor_id')->toArray();

        return Vendor::with(['media'])->whereActive(true)->whereIn('id', $subscribes)->get(['id', 'name', 'slug']);
    }

    /**
     * @return HasMany
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }

    /**
     * @return mixed
     */
    public function tags()
    {
        return Tag::with([
            'products' => fn ($q) => $q->select('products.id')
        ])->join('product_tag AS pt', 'pt.tag_id', '=', 'tags.id')
            ->join('products as p', 'p.id', '=', 'pt.product_id')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->where('c.id', '=', $this->id)->distinct('tags.id')->get(['tags.id', 'tags.name', 'tags.slug']);
    }

    /**
     * @return BelongsToMany
     */
    public function memberships(): BelongsToMany
    {
        return $this->belongsToMany(Membership::class, 'categories_memberships', 'category_id', 'membership_id');
    }
}
