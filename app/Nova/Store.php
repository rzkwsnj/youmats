<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Outl1ne\MultiselectField\Multiselect;
use Outl1ne\NovaSimpleRepeatable\SimpleRepeatable;

class Store extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Store::class;

    public static $title = 'name';

    public static $perPageViaRelationship = 25;

    public static $search = ['name'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            Text::make('name')
                ->rules(REQUIRED_STRING_VALIDATION)
                ->translatable(),

            Multiselect::make('Vendors', 'vendors')
                ->options(\App\Models\Vendor::pluck('name', 'id'))
                ->saveAsJSON(),

            SimpleRepeatable::make('Sub Categories', 'sub_categories', [
                Select::make('Category', 'category')
                    ->options(\App\Models\Category::where('parent_id', '>', 0)->pluck('name', 'id')),

                Number::make('Minimum Order Quantity', 'store_moq')
                    ->default(1)->min(1)
                    ->rules(NULLABLE_INTEGER_VALIDATION)
            ]),

            SimpleRepeatable::make('Contacts', 'contacts', [
                Text::make('Person Name', 'person_name')
                    ->rules(REQUIRED_STRING_VALIDATION),
                Text::make('Email', 'email')
                    ->rules(REQUIRED_EMAIL_VALIDATION),
                Number::make('Call Phone', 'call_phone')
                    ->rules(NULLABLE_NUMERIC_VALIDATION),
                Number::make('Whatsapp Phone', 'phone')
                    ->rules(NULLABLE_NUMERIC_VALIDATION),
                Text::make('Code', 'phone_code')
                    ->readonly(),
                Multiselect::make('Cities', 'cities')
                    ->options(\App\Models\City::pluck('name', 'id'))
                    ->saveAsJSON(),
                Select::make('With?', 'with')
                    ->options([
                        'individual' => 'Individual',
                        'company' => 'Company',
                        'both' => 'Both'
                    ])
            ])

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
