<?php

namespace App\Nova\Metrics;

use App\Models\Contact;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ContactCount extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(Contact::count())->suffix('Contacts');
    }

    public function ranges()
    {
        return [];
    }
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    public function uriKey()
    {
        return 'contact-count';
    }
}
