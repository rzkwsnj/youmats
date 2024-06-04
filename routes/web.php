<?php

use App\Http\Controllers\Front\Vendor\Admin\BranchController;
use App\Http\Controllers\Front\Vendor\Admin\GenerateProductController;
use App\Http\Controllers\Front\Vendor\Admin\IndexController;
use App\Http\Controllers\Front\Vendor\Admin\OrderController;
use App\Http\Controllers\Front\Vendor\Admin\ProductController;
use App\Http\Controllers\Front\Vendor\Admin\QuoteController;
use App\Http\Controllers\Front\Vendor\Admin\SippingGroupController;
use App\Http\Controllers\Front\Vendor\Admin\SubScribeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Statistics\LoggerController;

// Redirect 301 from admin panel
try {
    foreach (json_decode(nova_get_setting('redirect')) as $redirect) {
        Route::permanentRedirect($redirect->from, $redirect->to);
    }
} catch (Exception $e) {
}

Route::group([
    'prefix' => 'statistics',
    'middleware' => ['auth:admin'],
    'as' => 'statistics.'
], function () {
    Route::get('dashboard', [LoggerController::class, 'getLogs'])->name('log.dashboard');
    Route::get('categories', [LoggerController::class, 'getCategoryLogs'])->name('log.categories');
    Route::get('category/{category_id}', [LoggerController::class, 'SingleCategoryLogs'])->name('log.category');
    Route::get('vendors', [LoggerController::class, 'getVendorsLogs'])->name('log.vendors');
    Route::get('vendor/{vendor_id}', [LoggerController::class, 'SingleVendorLogs'])->name('log.vendor');
    Route::get('products', [LoggerController::class, 'getProductsLogs'])->name('log.products');
    Route::get('product/{product_id}', [LoggerController::class, 'SingleProductLogs'])->name('log.product');

    Route::get('origins', [LoggerController::class, 'OriginsLogs'])->name('log.origins');
    Route::get('counter/{counter_name}', [LoggerController::class, 'CounterLogs'])->name('log.counter');
});

//Actions routes
Route::post('changeCity', 'Common\MiscController@changeCity')->name('front.changeCity');
Route::post('changeCurrency', 'Common\MiscController@changeCurrency')->name('front.currencySwitch');
Route::get('introduce/{type}', 'Common\MiscController@introduce')->name('front.introduce');


Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localizationRedirect']
], function () {

    // Redirect 301 for products links from old website
    Route::permanentRedirect('Product-View/{productId}/{productSlug}', env('APP_URL'));


    //Auth (Verified/Authenticated) routes
    Route::group(['namespace' => 'User'], function () {
        Auth::routes(['verify' => true]);
        Route::group([
            'middleware' => ['auth', 'verified']
        ], function () {
            Route::get('/user/profile', 'ProfileController@index')->name('front.user.profile');
            Route::post('/user/profile', 'ProfileController@updateProfile')->name('front.user.updateProfile');
        });
    });

    //    Route::group(['prefix' => 'chat', 'namespace' => 'Chat', 'as' => 'chat.'], function () {
    //        Route::get('user/conversations/{vendor_id}', 'MessageController@userConversations')->name('user.conversations');
    //        Route::get('vendor/conversations/{user_id}', 'MessageController@vendorConversations')->name('vendor.conversations');
    //        Route::post('send_message', 'MessageController@sendMessage')->name('send_message');
    //    });

    // Vendor Routes
    Route::group(['prefix' => 'vendor', 'namespace' => 'Vendor', 'as' => 'vendor.'], function () {
        Auth::routes(['verify' => true]);

        Route::get('dashboard', [IndexController::class, 'dashboard'])->name('dashboard');

        Route::get('edit', [IndexController::class, 'edit'])->name('edit');
        Route::put('update', [IndexController::class, 'update'])->name('update');

        Route::group(['prefix' => 'subscribe', 'as' => 'subscribe.'], function () {
            Route::get('/', [SubScribeController::class, 'index'])->name('index');
            Route::get('/upgrade', [SubScribeController::class, 'upgrade'])->name('upgrade');
            Route::post('/submit-payment', [SubScribeController::class, 'submit'])->name('submit');
            Route::post('/cancel-subscribe', [SubScribeController::class, 'cancel'])->name('cancel');
            Route::get('/success', [SubScribeController::class, 'success'])->name('success');
            Route::get('/error', [SubScribeController::class, 'error'])->name('error');
        });

        Route::get('getSubCategories/{has_template?}', [IndexController::class, 'getSubCategories'])->name('category.getSub');
        Route::get('getAttributes', [IndexController::class, 'getAttributes'])->name('category.getAttr');
        Route::get('getTemplate', [IndexController::class, 'getTemplate'])->name('category.getTemplate');

        Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/store', [ProductController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [ProductController::class, 'update'])->name('update');
            Route::get('/deleteImage/{product}/{image}', [ProductController::class, 'deleteImage'])->name('deleteImage');

            Route::get('/generate', [GenerateProductController::class, 'generate'])->name('generate');
            Route::post('/generate', [GenerateProductController::class, 'requestGenerate'])->name('request.generate');
        });
        Route::group(['prefix' => 'branch', 'as' => 'branch.'], function () {
            Route::get('/', [BranchController::class, 'index'])->name('index');
            Route::get('/create', [BranchController::class, 'create'])->name('create');
            Route::post('/store', [BranchController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [BranchController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [BranchController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [BranchController::class, 'delete'])->name('delete');
        });
        Route::group(['prefix' => 'shipping-group', 'as' => 'shipping-group.'], function () {
            Route::get('/', [SippingGroupController::class, 'index'])->name('index');
            Route::get('/create', [SippingGroupController::class, 'create'])->name('create');
            Route::post('/store', [SippingGroupController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [SippingGroupController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [SippingGroupController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [SippingGroupController::class, 'delete'])->name('delete');
        });

        Route::get('order', [OrderController::class, 'index'])->name('order.index');
        Route::get('order/edit/{id}', [OrderController::class, 'edit'])->name('order.edit');
        Route::post('order/update', [OrderController::class, 'update'])->name('order.update');

        Route::get('quote', [QuoteController::class, 'index'])->name('quote.index');
        Route::get('quote/view/{id}', [QuoteController::class, 'view'])->name('quote.view');
        //        Route::post('quote/update', [QuoteController::class, 'update'])->name('quote.update');
    });

    //Cart Routes
    Route::group(['prefix' => 'cart', 'namespace' => 'Product'], function () {
        Route::get('/', 'CartController@show')->name('cart.show');
        Route::post('/add/{product}', 'CartController@add')->name('cart.add')->middleware('throttle:10,1');
        Route::post('/delivery_warning/{product}', 'CartController@delivery_warning')->name('cart.delivery_warning');
        Route::delete('/delete/{rowId}', 'CartController@deleteItem')->name('cart.remove');
        Route::patch('/update', 'CartController@update')->name('cart.update')->middleware('throttle:10,1');
        Route::post('/coupon', 'CartController@applyCoupon')->name('apply.coupon');
    });
    /*
    Route::group(['prefix' => 'wishlist', 'namespace' => 'Product', 'middleware' => ['auth', 'verified']], function() {
        Route::get('/', 'WishlistController@index')->name('wishlist.index');
        Route::post('/add/{product}', 'WishlistController@add')->name('wishlist.add');
        Route::delete('/delete/{rowId}', 'WishlistController@deleteItem')->name('wishlist.remove');
    });
*/
    Route::group(['prefix' => 'checkout', 'namespace' => 'Product'], function () {
        Route::get('/', 'CheckoutController@index')->name('checkout.index');
        Route::post('/', 'CheckoutController@checkout')->name('checkout');

        Route::get('/payment', 'PaymentController@form')->name('payment.form');
        Route::post('/submit-payment', 'PaymentController@submit')->name('payment.submit');
        Route::get('/success', 'PaymentController@success')->name('payment.success');
        Route::get('/error', 'PaymentController@error')->name('payment.error');
    });

    //Pages routes
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/products', 'Product\ProductController@all')->name('front.product.all');
    Route::get('/team', 'Team\IndexController@index')->name('front.team.index');
    Route::get('/FAQs', 'Common\PageController@faqs')->name('front.faqs.page');
    Route::get('/contact-us', 'Common\PageController@contactUs')->name('front.contact.page');

    // Search Bar
    Route::get('/search/{searched_word}', 'Product\ProductController@search')->name('products.search');
    Route::get('/suggest', 'Product\ProductController@suggest')->name('products.suggest');
    // Search Bar

    Route::post('/contact-us', 'Common\PageController@contactUsRequest')->name('front.contact.request');
    Route::post('/subscribe', 'Common\MiscController@subscribeRequest')->name('front.subscribe.request');
    Route::post('/inquire', 'Common\MiscController@inquireRequest')->name('front.inquire.request');

    Route::get('/page/{slug}', 'Common\PageController@page')->name('front.page.index');

    Route::get('/tag', 'Tag\IndexController@tag')->name('front.tag.tag');
    Route::get('/tag/{tag_slug}', 'Tag\IndexController@index')->name('front.tag');

    Route::get('/shop', 'Tag\IndexController@shop')->name('front.tag.shop');
    Route::get('/shop/{search_keyword}', 'Tag\IndexController@searchKeywordsTags')->name('front.tag.search');

    Route::get('/blog', 'Blog\IndexController@index')->name('front.blog.index');
    Route::get('/blog/{slug}', 'Blog\IndexController@show')->name('front.blog.show');

    Route::get('/suppliers', 'Vendor\IndexController@index')->name('vendor.index');
    Route::get('/suppliers/{vendor_slug}', 'Vendor\IndexController@show')->name('vendor.show');

    Route::permanentRedirect('/partners', '/suppliers');
    Route::permanentRedirect('/partners/{vendor_slug}', '/suppliers/{vendor_slug}');

    // Route::get('/PhoneCall/{slug}', 'Common\TwilioController@call')->name('api.twilio');

    Route::get('/{categories_slug}/{slug}/i', 'Product\ProductController@index')
        ->name('front.product')->where('categories_slug', '.*');

    Route::get('/{slug}', 'Category\CategoryController@index')
        ->name('front.category')->where('slug', '.*')
        ->where('slug', '^(?!management|nova-api|nova-vendor).*$');
});
