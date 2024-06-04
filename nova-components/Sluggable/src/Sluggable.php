<?php

namespace Zakariatlilani\Sluggable;

use Laravel\Nova\Fields\Slug;

class Sluggable extends Slug
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'Sluggable';
}
