<?php

namespace App\Models;

use App\Helpers\Classes\Shipping as ShippingHelper;
use App\Helpers\Traits\DefaultImage;
use App\Helpers\Traits\UnicodeJsonColumn;
use Gloudemans\Shoppingcart\Contracts\Buyable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;
use Znck\Eloquent\Traits\BelongsToThrough;

class Product extends Model implements Sortable, HasMedia, Buyable
{
    use SoftDeletes, HasFactory, SortableTrait, HasTranslations, InteractsWithMedia, DefaultImage, BelongsToThrough, UnicodeJsonColumn;

    protected $guarded = ['id'];

    public $fillable = ['name', 'temp_name', 'desc', 'short_desc', 'search_keywords', 'meta_title', 'meta_keywords', 'meta_desc', 'canonical', 'rate', 'stores', 'min_quantity'];

    public $translatable = ['desc', 'short_desc', 'search_keywords', 'meta_title', 'meta_keywords', 'meta_desc', 'canonical'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url', 'delivery', 'contacts'];

    protected $casts = [
        'shipping_prices' => 'array',
        'stores' => 'array',
        'rate' => 'string'
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('size_height_50')
            ->height(50)
            ->nonQueued()
            ->performOnCollections(PRODUCT_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_50_50')
            ->crop(Manipulations::CROP_CENTER, 50, 50)
            ->nonQueued()
            ->performOnCollections(PRODUCT_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_150')
            ->height(150)
            ->nonQueued()
            ->performOnCollections(PRODUCT_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_width_100')
            ->width(100)
            ->nonQueued()
            ->performOnCollections(PRODUCT_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_150_150')
            ->crop(Manipulations::CROP_CENTER, 150, 150)
            ->nonQueued()
            ->performOnCollections(PRODUCT_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_300')
            ->height(300)
            ->nonQueued()
            ->performOnCollections(PRODUCT_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_300_300')
            ->crop(Manipulations::CROP_CENTER, 300, 300)
            ->nonQueued()
            ->performOnCollections(PRODUCT_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_500')
            ->height(500)
            ->nonQueued()
            ->performOnCollections(PRODUCT_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_500_500')
            ->crop(Manipulations::CROP_CENTER, 500, 500)
            ->nonQueued()
            ->performOnCollections(PRODUCT_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(PRODUCT_PATH)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(PRODUCT_PATH);
    }

    public function getPriceAttribute($value)
    {
        return round($value * getCurrency('rate'), 2);
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2);
    }

    public function getCostAttribute($value)
    {
        return round($value * getCurrency('rate'), 2);
    }

    public function getLocationAttribute()
    {
        return $this->vendor->select('latitude', 'longitude')->first();
    }

    /**
     * @return array|null
     */
    public function getDeliveryAttribute()
    {
        try {
            $remap_shipping = [];
            if ($this->specific_shipping && $this->shipping_prices) {
                $remap_shipping = ShippingHelper::remap($this->shipping_prices, false);
            } elseif (isset($this->shipping) && $this->shipping->prices) {
                $remap_shipping = ShippingHelper::remap($this->shipping->prices);
            }
            foreach ($remap_shipping as $city => $shipping) {
                if (Session::has('city') && $city == Session::get('city')) {
                    return ShippingHelper::result(ShippingHelper::getBestPrice($shipping, $this->min_quantity ?? 1));
                }
            }
            return null;
        } catch (\Exception $e) {
        }
    }

    /**
     * @return string|null
     */
    public function delivery_cities()
    {
        try {
            $cities = [];
            if ($this->specific_shipping) {
                if ($this->shipping_prices) {
                    foreach ($this->shipping_prices as $shipping) {
                        foreach ($shipping['attributes']['cities'] as $city) {
                            $cities[] = $city['city'];
                        }
                    }
                }
            } elseif (isset($this->shipping)) {
                if ($this->shipping->prices) {
                    foreach ($this->shipping->prices as $shipping) {
                        foreach ($shipping['attributes']['cities'] as $city) {
                            $cities[] = $city['attributes']['city'];
                        }
                    }
                }
            }
            if (count($cities))
                return City::whereIn('id', array_unique($cities))->get();
            return null;
        } catch (\Exception $e) {
        }
    }

    public function getContactsAttribute()
    {
        try {
            if (isset($this->vendor->contacts)) {
                foreach ($this->vendor->contacts as $contact) {
                    if (
                        isset($contact['cities']) && Session::has('userType') && Session::has('city')
                        && ($contact['with'] == Session::get('userType') || $contact['with'] == 'both')
                    ) {
                        foreach ($contact['cities'] as $city) {
                            if ($city == Session::get('city')) {
                                return 1;
                            }
                        }
                    }
                }
                return 0;
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * @return int
     */
    public function getSubscribeAttribute()
    {
        if (
            isset($this->vendor->current_subscribes)
            && count($this->vendor->current_subscribes)
            && array_intersect(
                array_merge(
                    [$this->category_id],
                    $this->category->getRelation('ancestors')->pluck('id')->toArray()
                ),
                $this->vendor->current_subscribes->pluck('category_id')->toArray()
            )
        ) {
            return 1;
        }
        return 0;
    }

    /**
     * @return string
     */
    public function whatsapp_message(): string
    {
        $integration_number = nova_get_setting('whatsapp_manage_by_admin');
        $message = route('front.product', [generatedNestedSlug($this->category->getRelation('ancestors')->pluck('slug')->toArray(), $this->category->slug), $this->slug]);
        $vendor = $this->vendor;
        if (!$vendor->manage_by_admin) {

            if (nova_get_setting('enable_encryption_mode') || $vendor->enable_encryption_mode) {
                $integration_number = nova_get_setting('whatsapp_integration');
                $phone_code = ';;' . get_contact($vendor, 'phone_code') . ';;';
                $vendor_code = ';;' . vendor_encrypt($vendor) . ';;';
                $category_name = ';;' . $this->category->name . ';;';
                $message .= '%0A,%0A' . $phone_code;
                $message .= '%0A,%0A' . $vendor_code;
                $message .= '%0A,%0A' . $category_name;
            } else {
                $integration_number = (get_contact($vendor, 'phone')) ?? nova_get_setting('whatsapp_manage_by_admin');
            }
        }

        return 'https://wa.me/' . $integration_number . '?text=' . $message;
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * @return BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * @return BelongsToMany
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_values_products', 'product_id', 'attribute_id');
    }

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return BelongsTo
     */
    public function shipping(): BelongsTo
    {
        return $this->belongsTo(Shipping::class);
    }

    /**
     * @param null $options
     * @return int|mixed|string
     */
    public function getBuyableIdentifier($options = null)
    {
        return $this->id;
    }

    /**
     * @param null $options
     * @return string
     */
    public function getBuyableDescription($options = null): string
    {
        return $this->name;
    }

    /**
     * @param null $options
     * @return float
     */
    public function getBuyablePrice($options = null): float
    {
        return $this->price;
    }

    /**
     * @param $value
     * @return array
     */
    public function getImageUrlAttribute($value): array
    {
        return $this->getFirstMediaUrlOrDefault(PRODUCT_PATH);
    }

    /**
     * @param $query
     * @param $price
     * @return mixed
     */
    public function scopePrice($query, $price)
    {
        $prices = explode(';', $price);
        return $query->where('price', '>=', $prices[0])
            ->where('price', '<=', $prices[1]);
    }
}
