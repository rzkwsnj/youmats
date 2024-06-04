<?php

namespace App\Nova;

use App\Helpers\Nova\Fields;
use App\Nova\Filters\VendorType;
use Carbon\Carbon;
use Davidpiesse\NovaToggle\Toggle;
use Inspheric\Fields\Url;
use Laravel\Nova\Nova;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\MultiselectField\Multiselect;
use Outl1ne\NovaSimpleRepeatable\SimpleRepeatable;
use YieldStudio\NovaPhoneField\PhoneNumber;

class Vendor extends Resource
{
    public static $model = \App\Models\Vendor::class;

    public static $title = 'name';

    public static $perPageViaRelationship = 25;

    public static $search = [
        'name', 'slug', 'email'
    ];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            BelongsTo::make('Country')
                ->showCreateRelationButton()
                ->hideFromIndex()
                ->withoutTrashed(),

            Text::make('Name')
                ->sortable()
                ->rules(REQUIRED_STRING_VALIDATION)->hideFromIndex()
                ->translatable(),

            Text::make(
                'Name',
                'name',
                fn () =>
                '<a href="' . Nova::path() . "/resources/{$this->uriKey()}/{$this->id}" . '" class="font-bold no-underline dim text-primary">' . $this->name . '</a>'
            )->asHtml()->onlyOnIndex(),

            PhoneNumber::make('Call Phone', 'call_phone')
                ->withCustomFormats('(+966) ###-###-###')
                ->onlyOnIndex()
                ->rules(NULLABLE_STRING_VALIDATION),

            PhoneNumber::make('Whatsapp Phone', 'phone')
                ->withCustomFormats('(+966) ###-###-###')
                ->sortable()
                ->onlyOnIndex()
                ->rules(NULLABLE_STRING_VALIDATION),

            Text::make('Main Email', 'email')
                ->sortable()
                ->hideFromIndex()
                ->help('It will be used to login')
                ->rules(REQUIRED_EMAIL_VALIDATION)
                ->creationRules('unique:vendors,email')
                ->updateRules('unique:vendors,email,{{resourceId}}'),

            PhoneNumber::make('Main Phone', 'phone')
                ->withCustomFormats('(+966) ###-###-###')
                ->hideFromIndex()
                ->rules(REQUIRED_STRING_VALIDATION),

            Text::make('Address')
                ->rules(NULLABLE_STRING_VALIDATION)
                ->hideFromIndex(),

            SimpleRepeatable::make('Contacts', 'contacts', [
                Text::make('Person Name', 'person_name')
                    ->rules(REQUIRED_STRING_VALIDATION),
                Text::make('Email', 'email')
                    ->rules(REQUIRED_EMAIL_VALIDATION),
                PhoneNumber::make('Call Phone', 'call_phone')
                    ->withCustomFormats('(+966) ###-###-###')
                    ->rules(NULLABLE_NUMERIC_VALIDATION),
                PhoneNumber::make('Whatsapp Phone', 'phone')
                    ->withCustomFormats('(+966) ###-###-###')
                    ->rules(NULLABLE_NUMERIC_VALIDATION),
                PhoneNumber::make('Code', 'phone_code')
                    ->withCustomFormats('(+966) ###-###-###')
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
                    ->rules(REQUIRED_STRING_VALIDATION),
            ]),

            Select::make('Type')->options([
                'factory' => 'Factory',
                'distributor' => 'Distributor',
                'wholesales' => 'Wholesales',
                'retail' => 'Retail'
            ])->displayUsingLabels()->hideFromIndex()
                ->rules([...NULLABLE_STRING_VALIDATION, 'In:factory,distributor,wholesales,retail']),

            Text::make('Facebook', 'facebook_url')
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),
            Text::make('Twitter', 'twitter_url')
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),
            Text::make('Youtube', 'youtube_url')
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),
            Text::make('Instagram', 'instagram_url')
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),
            Text::make('Pinterest', 'pinterest_url')
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),
            Text::make('Website', 'website_url')
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),

            Fields::image(true, VENDOR_COVER, 'Cover', true),

            Fields::image(true, VENDOR_LOGO, 'Logo', true),

            Fields::file(true, VENDOR_PATH, 'Licenses', false),

            Toggle::make('Active')->sortable(),

            Toggle::make('Sold by ' . env('APP_NAME'), 'sold_by_youmats')->sortable(),

            Toggle::make('Manage by ' . env('APP_NAME'), 'manage_by_admin')->sortable(),

            Toggle::make('Enable Encryption Mode', 'enable_encryption_mode')->sortable(),

            Toggle::make('Enable 3CX', 'enable_3cx')->sortable(),

            Toggle::make('Featured', 'isFeatured')->sortable(),

            Date::make('Signup Date', 'created_at')
                ->withMeta(['value' => Carbon::now()])
                ->hideFromIndex()
                ->rules(REQUIRED_DATE_VALIDATION)
                ->default(Carbon::now()->toDateTimeString())
                ->sortable(),

            Url::make('Link')
                ->customHtmlUsing(function ($value, $resource, $label) {
                    return view('vendor.nova.partials.custom_link', [
                        'url'   => route('vendor.show', [$this->slug]),
                        'label' => 'Link',
                    ])->render();
                })->exceptOnForms(),

            Password::make('Password')->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Fields::SEO(static::$model, 'vendors'),

            HasMany::make('Products'),
            HasMany::make('Branches'),
            HasMany::make('Shippings'),
            HasMany::make('Categories'),
            HasMany::make('Subscribes'),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [];
    }

    public function filters(NovaRequest $request)
    {
        return [
            new VendorType
        ];
    }

    public function lenses(NovaRequest $request)
    {
        return [];
    }

    public function actions(NovaRequest $request)
    {
        return [];
    }
}
