<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class FAQ extends Model implements Sortable
{
    use SoftDeletes, HasFactory, SortableTrait, HasTranslations, UnicodeJsonColumn;

    protected $table = 'faqs';
    public $fillable = ['question', 'answer'];
    public $translatable = ['question', 'answer'];
}
