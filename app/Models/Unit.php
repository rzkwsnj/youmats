<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class Unit extends Model implements Sortable
{
    use HasFactory, SortableTrait, HasTranslations, UnicodeJsonColumn;

    public $fillable = ['name'];
    public $translatable = ['name'];
}
