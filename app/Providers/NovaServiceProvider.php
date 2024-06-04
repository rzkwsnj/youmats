<?php

namespace App\Providers;

use App\Models\Category as CategoryModel;
use App\Models\Product as ProductModel;
use App\Models\Vendor as VendorModel;
use App\Nova\Admin;
use App\Nova\Article;
use App\Nova\Attribute;
use App\Nova\Car;
use App\Nova\CarType;
use App\Nova\Category;
use App\Nova\City;
use App\Nova\Contact;
use App\Nova\Country;
use App\Nova\Coupon;
use App\Nova\Currency;
use App\Nova\Driver;
use App\Nova\ErrorLog;
use App\Nova\FAQ;
use App\Nova\GenerateProduct;
use App\Nova\Inquire;
use App\Nova\Language;
use App\Nova\Membership;
use App\Nova\Dashboards\Main;
use App\Nova\Order;
use App\Nova\StaticPage;
use App\Nova\Partner;
use App\Nova\Product;
use App\Nova\Quote;
use App\Nova\Slider;
use App\Nova\StaticImage;
use App\Nova\Store;
use App\Nova\Subscribe;
use App\Nova\Subscriber;
use App\Nova\Tag;
use App\Nova\Team;
use App\Nova\Trip;
use App\Nova\Unit;
use App\Nova\User;
use App\Nova\Vendor;
use App\Observers\CategoryObserver;
use App\Observers\ProductObserver;
use App\Observers\VendorObserver;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use Davidpiesse\NovaToggle\Toggle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Mostafaznv\NovaCkEditor\CkEditor;
use Outl1ne\NovaSettings\NovaSettings;
use Outl1ne\NovaSimpleRepeatable\SimpleRepeatable;
use Vyuldashev\NovaPermission\NovaPermissionTool;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    public function boot()
    {
        parent::boot();

        try {
            CategoryModel::fixTree();
            NovaSettings::addSettingsFields([
                new Panel('General', $this->generalData()),
                new Panel('General SEO', $this->generalSeo()),
                new Panel('General Blog SEO', $this->generalBlogSeo()),
                new Panel('Social Media Links', $this->socialFields()),
            ], [], 'General');

            NovaSettings::addSettingsFields([
                new Panel('Vendor Terms', $this->vendorTerms())
            ], [], 'Vendor Terms');

            NovaSettings::addSettingsFields([
                new Panel('Redirect 301', $this->redirect301())
            ], [], 'Redirect 301');

            Nova::serving(function () {
                CategoryModel::observe(CategoryObserver::class);
                ProductModel::observe(ProductObserver::class);
                VendorModel::observe(VendorObserver::class);
            });
        } catch (\Exception $e) {
        }

        Nova::withBreadcrumbs();


        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::dashboard(Main::class)->icon('home'),
                MenuSection::make('Products', [
                    MenuItem::resource(Product::class),
                    MenuItem::resource(Category::class),
                    MenuItem::resource(Tag::class),
                    MenuItem::resource(GenerateProduct::class),
                ])->icon('color-swatch')->collapsedByDefault(),
                MenuSection::make('Orders', [
                    MenuItem::resource(Order::class),
                    MenuItem::resource(Quote::class),
                ])->icon('clipboard')->collapsedByDefault(),
                MenuSection::make('Users', [
                    MenuItem::resource(User::class),
                    MenuItem::resource(Subscriber::class),
                    MenuItem::resource(Contact::class),
                    MenuItem::resource(Inquire::class),
                ])->icon('user-group')->collapsedByDefault(),
                MenuSection::make('Vendors', [
                    MenuItem::resource(Vendor::class),
                    MenuItem::resource(Store::class),
                    MenuItem::resource(Subscribe::class),
                    MenuItem::resource(Membership::class)
                ])->icon('users')->collapsedByDefault(),
                MenuSection::make('Tracker', [
                    MenuItem::resource(Trip::class),
                    MenuItem::resource(Driver::class),
                    MenuItem::resource(Car::class),
                    MenuItem::resource(CarType::class)
                ])->icon('truck')->collapsedByDefault(),
                MenuSection::make('Blog', [
                    MenuItem::resource(Article::class),
                ])->icon('pencil-alt')->collapsedByDefault(),
                MenuSection::make('Management', [
                    MenuItem::resource(Admin::class),
                    MenuItem::resource(StaticImage::class),
                    MenuItem::resource(Partner::class),
                    MenuItem::resource(Slider::class),
                    MenuItem::resource(Team::class),
                    MenuItem::resource(StaticPage::class),
                    MenuItem::resource(FAQ::class),
                    MenuItem::resource(ErrorLog::class),
                    MenuItem::externalLink('Statistics', route('statistics.log.dashboard')),
                ])->icon('cog')->collapsedByDefault(),

                (MenuSection::make('General settings', [
                    MenuGroup::make('Countries', [
                        MenuItem::resource(Country::class),
                        MenuItem::resource(City::class)
                    ])->collapsedByDefault(),
                    MenuItem::resource(Language::class),
                    MenuItem::resource(Currency::class),
                    MenuItem::resource(Unit::class),
                    MenuItem::resource(Attribute::class),
                    MenuItem::resource(Coupon::class),
                    MenuItem::Link('Youmats settings', 'nova-settings/general'),
                    MenuItem::Link('Vendor terms', 'nova-settings/vendor-terms'),
                    MenuItem::Link('Redirect 301', 'nova-settings/redirect-301'),
                    MenuItem::Link('Translation', 'nova-translation-editor/index'),
                    MenuItem::Link('Activity logs', 'resources/action-events')
                ])->icon('finger-print')->collapsedByDefault()),

                (MenuSection::make('Roles & Permissions', [
                    MenuItem::Link('Permissions', 'resources/permissions'),
                    MenuItem::Link('Roles', 'resources/roles'),
                ])->icon('ban')->collapsedByDefault())
            ];
        });

        Nova::footer(function ($request) {
            return Blade::render('
                    <div class="text-center">
                        <p class="text-base">
                            Made with 
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 inline" style="color:rgb(225 29 72);">
                                <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                            </svg>
                            by <a href="https://github.com/ZakariaTlilani/" style="color:rgb(3 105 161);">Zakaria Tlilani</a>.
                         </p>
                    </div>
            ');
        });
    }

    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    protected function dashboards()
    {
        return [
            new Main,
        ];
    }

    public function tools()
    {
        return [
            NovaPermissionTool::make()
                ->rolePolicy(RolePolicy::class)
                ->permissionPolicy(PermissionPolicy::class),
            NovaSettings::make(),
            new \Zakariatlilani\NovaTranslationEditor\NovaTranslationEditor()
        ];
    }

    public function register()
    {
    }

    /**
     * @return array
     */
    private function socialFields(): array
    {
        return [
            Text::make('Facebook')->rules(NULLABLE_URL_VALIDATION),
            Text::make('Twitter')->rules(NULLABLE_URL_VALIDATION),
        ];
    }

    private function generalData(): array
    {
        return [
            Text::make('Site Name', 'site_name')
                ->rules(REQUIRED_STRING_VALIDATION)->translatable(),
            Text::make('Main Phone', 'phone')
                ->rules(REQUIRED_STRING_VALIDATION),
            Text::make('Widget Phone', 'widget_phone')
                ->rules(REQUIRED_STRING_VALIDATION),
            Text::make('Widget Whatsapp', 'widget_whatsapp')
                ->rules(REQUIRED_STRING_VALIDATION),
            Text::make('Whatsapp Integration', 'whatsapp_integration')
                ->rules(REQUIRED_STRING_VALIDATION),
            Text::make('Whatsapp Manage by ' . env('APP_NAME'), 'whatsapp_manage_by_admin')
                ->rules(REQUIRED_STRING_VALIDATION),
            Toggle::make('Enable Encryption Mode',  'enable_encryption_mode'),
            Toggle::make('Enable 3CX',  'enable_3cx'),
            Toggle::make('Enable Phone Buttons',  'enable_phone_buttons'),
            Toggle::make('Enable Whatsapp redirect',  'enable_whatsapp_redirect'),

        ];
    }

    /**
     * @return array
     */
    private function generalSeo(): array
    {
        return [
            Text::make('Home Meta Title', 'home_meta_title')
                ->rules(NULLABLE_STRING_VALIDATION)->translatable(),

            Text::make('Home Meta Keywords', 'home_meta_keywords')
                ->rules(NULLABLE_TEXT_VALIDATION)->translatable(),

            Textarea::make('Home Meta Description', 'home_meta_desc')
                ->rules(NULLABLE_TEXT_VALIDATION)->translatable(),

            Code::make('Home Schema', 'home_schema')
                ->rules(NULLABLE_TEXT_VALIDATION),

            Text::make('Categories Additional Word', 'categories_additional_word')
                ->rules(NULLABLE_STRING_VALIDATION)->translatable(),

            Text::make('Products Additional Word', 'products_additional_word')
                ->rules(NULLABLE_STRING_VALIDATION)->translatable(),

        ];
    }


    /**
     * @return array
     */
    private function generalBlogSeo(): array
    {
        return [
            Text::make('Blog Home Meta Title', 'blog_home_meta_title')
                ->rules(NULLABLE_STRING_VALIDATION)->translatable(),

            Text::make('Blog Home Meta Keywords', 'blog_home_meta_keywords')
                ->rules(NULLABLE_TEXT_VALIDATION)->translatable(),

            Textarea::make('Blog Home Meta Description', 'blog_home_meta_desc')
                ->rules(NULLABLE_TEXT_VALIDATION)->translatable(),

            Code::make('Blog Home Schema', 'blog_home_schema')
                ->rules(NULLABLE_TEXT_VALIDATION),

        ];
    }



    /**
     * @return array
     */
    private function vendorTerms(): array
    {
        return [
            Text::make('Title', 'vendor_terms_title')
                ->rules(REQUIRED_STRING_VALIDATION)->translatable(),
            CkEditor::make('Text', 'vendor_terms_text')
                ->rules(REQUIRED_TEXT_VALIDATION)->translatable(),
            Text::make('Button', 'vendor_terms_button')
                ->rules(REQUIRED_STRING_VALIDATION)->translatable(),
        ];
    }

    /**
     * @return array
     */
    private function redirect301(): array
    {
        return [
            SimpleRepeatable::make('Redirect', 'redirect', [
                Text::make('From')->rules(REQUIRED_STRING_VALIDATION),
                Text::make('To')->rules(REQUIRED_STRING_VALIDATION)
            ])->canAddRows(true)->canDeleteRows(true),
        ];
    }
}
