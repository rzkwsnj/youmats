<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class Membership extends Model implements Sortable
{
    use SoftDeletes, HasFactory, SortableTrait, HasTranslations, CascadeSoftDeletes, UnicodeJsonColumn;

    protected $fillable = ['name', 'desc'];

    public $translatable = ['name', 'desc'];

    protected $casts = [
        'deleted_at' => 'datetime'
    ];

    protected $cascadeDeletes = ['vendors'];

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'categories_memberships', 'membership_id', 'category_id');
    }
}
