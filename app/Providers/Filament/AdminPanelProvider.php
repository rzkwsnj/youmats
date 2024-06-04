<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Resources\AdminResource;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\AttributeResource;
use App\Filament\Resources\CarResource;
use App\Filament\Resources\CarTypeResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CityResource;
use App\Filament\Resources\ContactResource;
use App\Filament\Resources\CountryResource;
use App\Filament\Resources\CurrencyResource;
use App\Filament\Resources\DriverResource;
use App\Filament\Resources\ErrorLogResource;
use App\Filament\Resources\FAQResource;
use App\Filament\Resources\InquireResource;
use App\Filament\Resources\LanguageResource;
use App\Filament\Resources\MembershipResource;
use App\Filament\Resources\NovaSettingsResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\PartnerResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\QuoteResource;
use App\Filament\Resources\SliderResource;
use App\Filament\Resources\StaticImageResource;
use App\Filament\Resources\StaticPageResource;
use App\Filament\Resources\StoreResource;
use App\Filament\Resources\SubscriberResource;
use App\Filament\Resources\TagResource;
use App\Filament\Resources\TeamResource;
use App\Filament\Resources\TripResource;
use App\Filament\Resources\UnitResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\VendorResource;
use App\Filament\Widgets\ClockWidget;
use App\Filament\Widgets\CommonStatsOverview;
use App\Filament\Widgets\OrderPerDayWidget;
use App\Filament\Widgets\OrderStatusWidget;
use App\Filament\Widgets\QuotePerDayWidget;
use App\Filament\Widgets\QuoteStatusWidget;
use App\Filament\Widgets\RevenueWidget;
use App\Filament\Widgets\UserPerDayWidget;
use App\Filament\Widgets\UserStatusWidget;
use App\Filament\Widgets\UserTypeWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Outerweb\FilamentTranslatableFields\Filament\Plugins\FilamentTranslatableFieldsPlugin;
use TomatoPHP\FilamentTranslations\FilamentTranslationsPlugin;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([

            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
//                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
                ClockWidget::class,
                UserTypeWidget::class,
                UserPerDayWidget::class,
                UserStatusWidget::class,
                RevenueWidget::class,
                OrderPerDayWidget::class,
                OrderStatusWidget::class,
                QuotePerDayWidget::class,
                QuoteStatusWidget::class,
                CommonStatsOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetTheme::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->resources([
                config('filament-logger.activity_resource')
            ])
            ->plugins([
                ThemesPlugin::make(),
                FilamentTranslatableFieldsPlugin::make()
                    ->supportedLocales([
                        'ar' => 'arabic',
                        'en' => 'English',
                    ]),
                FilamentSpatieRolesPermissionsPlugin::make(),
                FilamentTranslationsPlugin::make()
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->items([
                    NavigationItem::make('dashboard')
                        ->label(fn(): string => __('filament-panels::pages/dashboard.title'))
                        ->icon('heroicon-o-home')
                        ->url(fn(): string => Dashboard::getUrl())
                        ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.dashboard')),
                ])
                    ->groups([
                        NavigationGroup::make('Products')
                            ->items([
                                ...ProductResource::getNavigationItems(),
                                ...CategoryResource::getNavigationItems(),
                                ...TagResource::getNavigationItems(),

                            ]),
                        NavigationGroup::make('Orders')
                            ->items([
                                ...OrderResource::getNavigationItems(),
                                ...QuoteResource::getNavigationItems(),

                            ]),
                        NavigationGroup::make('Users')
                            ->items([
                                ...UserResource::getNavigationItems(),
                                ...SubscriberResource::getNavigationItems(),
                                ...ContactResource::getNavigationItems(),
                                ...InquireResource::getNavigationItems(),

                            ]),
                        NavigationGroup::make('Vendors')
                            ->items([
                                ...VendorResource::getNavigationItems(),
                                ...StoreResource::getNavigationItems(),
                                ...MembershipResource::getNavigationItems(),

                            ]),
                        NavigationGroup::make('Tracker')
                            ->items([
                                ...TripResource::getNavigationItems(),
                                ...DriverResource::getNavigationItems(),
                                ...CarResource::getNavigationItems(),
                                ...CarTypeResource::getNavigationItems(),

                            ]),

                        NavigationGroup::make('Blog')
                            ->items([
                                ...ArticleResource::getNavigationItems(),
                            ]),

                        NavigationGroup::make('Management')
                            ->items([
                                ...AdminResource::getNavigationItems(),
                                ...StaticImageResource::getNavigationItems(),
                                ...PartnerResource::getNavigationItems(),
                                ...SliderResource::getNavigationItems(),
                                ...TeamResource::getNavigationItems(),
                                ...StaticPageResource::getNavigationItems(),
                                ...FAQResource::getNavigationItems(),
                                ...ErrorLogResource::getNavigationItems(),
                                NavigationItem::make('Analytics')
                                    ->url(route('statistics.log.dashboard'), shouldOpenInNewTab: true)
                                    ->icon('heroicon-o-presentation-chart-line')
                                    ->group('Reports')
                                    ->sort(3),
                            ]),
                        NavigationGroup::make('General Settings')
                            ->items([
                                ...CountryResource::getNavigationItems(),
                                ...CityResource::getNavigationItems(),
                                ...LanguageResource::getNavigationItems(),
                                ...CurrencyResource::getNavigationItems(),
                                ...UnitResource::getNavigationItems(),
                                ...AttributeResource::getNavigationItems(),
                                ...NovaSettingsResource::getNavigationItems(),


                            ]),
                    ]);
            })/*
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Products')
                    ->icon('heroicon-o-shopping-cart'),

                NavigationGroup::make()
                    ->label('Orders')
                    ->icon('heroicon-o-shopping-cart'),

                NavigationGroup::make()
                    ->label('Users')
                    ->icon('heroicon-o-shopping-cart'),

                NavigationGroup::make()
                    ->label('Vendors')
                    ->icon('heroicon-o-shopping-cart'),

                NavigationGroup::make()
                    ->label('Vendors')
                    ->icon('heroicon-o-shopping-cart'),

                NavigationGroup::make()
                    ->label('Tracker')
                    ->icon('heroicon-o-shopping-cart'),

                NavigationGroup::make()
                    ->label('Blog')
                    ->icon('heroicon-o-shopping-cart'),

                NavigationGroup::make()
                    ->label('Management')
                    ->icon('heroicon-o-shopping-cart'),

                NavigationGroup::make()
                    ->label('General-Settings')
                    ->icon('heroicon-o-shopping-cart'),

            ])*/ ;
    }
}
