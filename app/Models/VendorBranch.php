<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class VendorBranch extends Model
{
    use HasFactory, HasTranslations, UnicodeJsonColumn;

    protected $fillable = ['name', 'website', 'fax', 'phone_number', 'latitude', 'longitude', 'address', 'vendor_id', 'city_id'];

    /**
     * @var string[]
     */
    protected $translatable = ['name'];

    protected $casts = [
        'location'           => 'array'
    ];

    /**
     * @return BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }
}
