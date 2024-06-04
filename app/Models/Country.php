<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class Country extends Model implements Sortable
{
    use SoftDeletes, HasFactory, SortableTrait, HasTranslations, CascadeSoftDeletes, UnicodeJsonColumn;

    public $fillable = ['name'];

    public $translatable = ['name'];

    protected $casts = [
        'deleted_at' => 'datetime'
    ];

    protected $cascadeDeletes = ['cities'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
}
