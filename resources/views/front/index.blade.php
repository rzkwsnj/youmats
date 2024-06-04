@extends('front.layouts.master')
@section('metaTags')
    <title>{{ (json_decode(nova_get_setting('home_meta_title'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}</title>
    <meta name="keywords" content="{{ (json_decode(nova_get_setting('home_meta_keywords'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}">
    <meta name="description" content="{{ (json_decode(nova_get_setting('home_meta_desc'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}">
    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="{{ (json_decode(nova_get_setting('home_meta_title'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}" />
    <meta property="og:description" content="{{ (json_decode(nova_get_setting('home_meta_desc'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}" />
    <meta property="og:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="{{ (json_decode(nova_get_setting('home_meta_title'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}">
    <meta name="twitter:description" content="{{ (json_decode(nova_get_setting('home_meta_desc'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}">
    <meta name="twitter:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}">

    {!! nova_get_setting('home_schema') !!}
@endsection
@section('content')

        @include('front.layouts.partials.change_city')


    <div class="mb-4">
        <div class="bg-img-hero" style="background-image: url({{ $staticImages->getFirstMediaUrlOrDefault(SLIDER_BACKGROUND_PATH, 'size_1920_438')['url'] }});">
            <div class="container min-height-438 overflow-hidden">
                <div class="js-slick-carousel u-slick" data-pagi-classes="text-center position-absolute right-0 bottom-0 left-0 u-slick__pagination u-slick__pagination--long justify-content-start mb-3 mb-md-4 offset-xl-2 pl-xl-16 pl-wd-13">
                    @foreach($sliders as $slider)
                    <div class="js-slide">
                        <div class="row min-height-438 pt-7 py-md-0">
                            <div class="d-none d-xl-block col-auto">
                                <div class="max-width-270 min-width-270"></div>
                            </div>
                            <div class="col-xl col col-md-6 mt-md-8 mt-lg-10">
                                <div class="ml-xl-4">
                                    <p class="font-size-15 font-weight-bold mb-2 text-cyan"
                                        data-scs-animation-in="fadeInUp">
                                        {{$slider->quote}}
                                    </p>
                                    <p class="font-size-46 text-lh-50 font-weight-light mb-6"
                                        data-scs-animation-in="fadeInUp"
                                        data-scs-animation-delay="200">
                                        <b class="font-weight-bold">{{$slider->title}}</b>
                                    </p>
                                    <a href="{{$slider->button_link}}" class="btn btn-primary transition-3d-hover rounded-lg font-weight-normal py-2 px-md-7 px-3 font-size-16"
                                       data-scs-animation-in="fadeInUp"
                                       data-scs-animation-delay="300">
                                        {{$slider->button_title}}
                                    </a>
                                </div>
                            </div>
                            <div class="col-xl-5 col-6 d-flex align-items-end ml-auto ml-md-0 slick-slide-img-content"
                                 data-scs-animation-in="fadeInUp"
                                 data-scs-animation-delay="500">
                                <img loading="lazy" class="img-fluid ml-auto mr-5" src="{{ $slider->getFirstMediaUrlOrDefault(SLIDER_PATH, 'size_400_270')['url'] }}" alt="{{$slider->getFirstMediaUrlOrDefault(SLIDER_PATH)['alt']}}" title="{{$slider->getFirstMediaUrlOrDefault(SLIDER_PATH)['title']}}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-xl-9 ml-auto col-wd-auto max-width-1045">
                <div class="row mb-6">
                    <div class="col-md-6 mb-4 mb-xl-0 col-wd-4">
                        <a href="{{__('homee.first_section_url')}}" class="d-black text-gray-90">
                            <div class="min-height-166 py-1 py-xl-2 py-wd-4 d-flex bg-gray-1 align-items-center">
                                <div class="col-6 col-xl-7 col-wd-6 pr-0">
                                    <img loading="lazy" class="img-fluid" src="{{ $staticImages->getFirstMediaUrlOrDefault(HOME_FIRST_SECTION_PATH, 'size_144_100')['url'] }}" title="{{__('homee.first_section')}}" alt="{{__('homee.first_section')}}">
                                </div>
                                <div class="col-6 col-xl-5 col-wd-6 pr-xl-4 pr-wd-3">
                                    <div class="mb-2 pb-1 font-size-18 font-weight-light text-ls-n1 text-lh-23">
                                        <strong>{{__('homee.first_section')}}</strong>
                                    </div>
                                    <div class="link text-gray-90 font-weight-bold font-size-15" href="{{__('homee.first_section_url')}}">
                                        {{__('general.read_more')}}
                                        <span class="link__icon ml-1">
                                            <span class="link__icon-inner"><i class="ec ec-arrow-right-categproes"></i></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-4 mb-xl-0 col-wd-4">
                        <a href="{{__('homee.second_section_url')}}" class="d-black text-gray-90">
                            <div class="min-height-166 py-1 py-xl-2 py-wd-4 d-flex bg-gray-1 align-items-center">
                                <div class="col-6 col-xl-7 col-wd-6 pr-0">
                                    <img loading="lazy" class="img-fluid" src="{{ $staticImages->getFirstMediaUrlOrDefault(HOME_SECOND_SECTION_PATH, 'size_144_100')['url'] }}" title="{{__('homee.second_section')}}" alt="{{__('homee.second_section')}}">
                                </div>
                                <div class="col-6 col-xl-5 col-wd-6 pr-xl-4 pr-wd-3">
                                    <div class="mb-2 pb-1 font-size-18 font-weight-light text-ls-n1 text-lh-23">
                                        <strong>{{__('homee.second_section')}}</strong>
                                    </div>
                                    <div class="link text-gray-90 font-weight-bold font-size-15" href="{{__('homee.second_section_url')}}">
                                        {{__('general.read_more')}}
                                        <span class="link__icon ml-1">
                                            <span class="link__icon-inner"><i class="ec ec-arrow-right-categproes"></i></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-4 mb-xl-0 col-wd-4 d-md-none d-wd-block">
                        <a href="{{__('homee.third_section_url')}}" class="d-black text-gray-90">
                            <div class="min-height-166 py-1 py-xl-2 py-wd-4 d-flex bg-gray-1 align-items-center">

                                <div class="col-6 col-xl-7 col-wd-6 pr-0">
                                    <img loading="lazy" class="img-fluid" src="{{ $staticImages->getFirstMediaUrlOrDefault(HOME_THIRD_SECTION_PATH, 'size_144_100')['url'] }}" title="{{__('homee.third_section')}}" alt="{{__('homee.third_section')}}">
                                </div>

                                <div class="col-6 col-xl-5 col-wd-6 pr-xl-4 pr-wd-3">
                                    <div class="mb-2 pb-1 font-size-18 font-weight-light text-ls-n1 text-lh-23">
                                        <strong>{{__('homee.third_section')}}</strong>
                                    </div>
                                    <div class="link text-gray-90 font-weight-bold font-size-15" href="{{__('homee.third_section_url')}}">
                                        {{__('general.read_more')}}
                                        <span class="link__icon ml-1">
                                            <span class="link__icon-inner"><i class="ec ec-arrow-right-categproes"></i></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('front.layouts.partials.vendors')

    @if(count($featured_categories))
    <div class="cat">
        <div class="container">
            <div class="row">
                <div class="box p-1 pr-2">
                    <h1 class="text-uppercase">{{__('homee.categories_subtitle')}}</h1>
                    <p>{{__('homee.catgeories_desc')}}</p>
                </div>
                @foreach($featured_categories as $f_category)
                <div class="box">
                    <a href="{{route('front.category', [generatedNestedSlug($f_category->getRelation('ancestors')->pluck('slug')->toArray(), $f_category->slug)])}}" class="st_block">
                        <img loading="lazy" src="{{$f_category->getFirstMediaUrlOrDefault(CATEGORY_COVER, 'size_255_364')['url']}}"
                             alt="{{$f_category->getFirstMediaUrlOrDefault(CATEGORY_COVER)['alt']}}"
                             title="{{$f_category->getFirstMediaUrlOrDefault(CATEGORY_COVER)['title']}}" />
                        <div class="content d-flex">
                            <h2 class="title">{{$f_category->name}}</h2>
                            <span class="text-blue">
                                <i class="fas fa-plus"></i>
                            </span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @include('front.layouts.partials.partners')

    @if(count($best_seller_products))
    <div class="mb-5">
        <div class="bg-img-hero bg_cat_new st_new pt-5">
            <div class="container p-0">
                <div class="d-flex justify-content-between border-bottom border-color-1 flex-lg-nowrap flex-wrap border-md-down-top-0 border-md-down-bottom-0 mb-3 rtl">
                    <h2 class="section-title section-title__full mb-0 pb-2 font-size-22">{{__('homee.featured_products')}}</h2>
                </div>
                <div class="mb-4 position-relative">
                    <div
                         class="js-slick-carousel u-slick u-slick--gutters-0 position-static overflow-hidden u-slick-overflow-visble pb-5 pt-2 px-1"
                         data-arrows-classes="u-slick__arrow u-slick__arrow--flat u-slick__arrow-centered--y rounded-circle"
                         data-pagi-classes="text-center right-0 bottom-1 left-0 u-slick__pagination u-slick__pagination--long mb-0 z-index-n1 mt-3 pt-1"
                         data-arrow-left-classes="fa fa-angle-left u-slick__arrow-classic-inner--left z-index-9"
                         data-arrow-right-classes="fa fa-angle-right u-slick__arrow-classic-inner--right"
                         data-slick='{
                            "autoplay": true,
                            "infinite": true,
                            "slidesToShow": 7
                            @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                            ,"rtl": true
                            @endif
                         }'
                         data-responsive='[{
                              "breakpoint": 1400,
                              "settings": {
                                "slidesToShow": 5
                              }
                            }, {
                                "breakpoint": 1200,
                                "settings": {
                                  "slidesToShow": 3
                                }
                            }, {
                              "breakpoint": 992,
                              "settings": {
                                "slidesToShow": 2
                              }
                            }, {
                              "breakpoint": 768,
                              "settings": {
                                "slidesToShow": 2
                              }
                            }, {
                              "breakpoint": 554,
                              "settings": {
                                "slidesToShow": 2
                              }
                         }]'
                    >
                        @foreach($best_seller_products as $bs_product)
                        <div class="js-slide products-group">
                            <div class="product-item mx-1 remove-divider">
                                <div class="product-item__outer h-100">
                                    <div class="product-item__inner px-xl-3 p-3">
                                        <div class="product-item__body pb-xl-2">
                                            <div class="mb-2">
                                                <a href="{{route('front.category', [generatedNestedSlug($bs_product->category->getRelation('ancestors')->pluck('slug')->toArray(), $bs_product->category->slug)])}}" class="font-size-12 text-gray-5">{{$bs_product->category->name}}</a>
                                            </div>
                                            <h3 class="mb-1 product-item__title">
                                                <a href="{{route('front.product', [generatedNestedSlug($bs_product->category->getRelation('ancestors')->pluck('slug')->toArray(), $bs_product->category->slug), $bs_product->slug])}}" class="text-blue font-weight-bold">{{$bs_product->name}}</a>
                                            </h3>
                                            <div class="mb-2">
                                                <a href="{{route('front.product', [generatedNestedSlug($bs_product->category->getRelation('ancestors')->pluck('slug')->toArray(), $bs_product->category->slug), $bs_product->slug])}}" class="d-block text-center">
                                                    <img loading="lazy" class="img-fluid" src="{{$bs_product->getFirstMediaUrlOrDefault(PRODUCT_PATH, 'size_150_150')['url']}}" alt="{{$bs_product->getFirstMediaUrlOrDefault(PRODUCT_PATH)['alt']}}" title="{{$bs_product->getFirstMediaUrlOrDefault(PRODUCT_PATH)['title']}}" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(count($featured_sections_categories))
    <div class="container">
        <div class="my-6">
            <!-- Nav nav-pills -->
            <div class="position-relative text-center z-index-2">
                <div class=" d-flex justify-content-between border-bottom border-color-1 flex-lg-nowrap flex-wrap border-md-down-top-0 border-md-down-bottom-0 rtl">
                    <h2 class="section-title mb-0 pb-2 font-size-22">{{$featured_sections_categories[0]->name}}</h2>
                </div>
            </div>
            <!-- End Nav Pills -->
            <div class="row rtl">
                <div class="col-md-3">
                    <div class="block_img_cat">
                        <a href="{{route('front.category', [generatedNestedSlug($featured_sections_categories[0]->getRelation('ancestors')->pluck('slug')->toArray(), $featured_sections_categories[0]->slug)])}}" class="d-block">
                            <img loading="lazy" class="img-fluid" src="{{$featured_sections_categories[0]->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_305_570')['url']}}"
                                 alt="{{$featured_sections_categories[0]->getFirstMediaUrlOrDefault(CATEGORY_PATH)['alt']}}"
                                 title="{{$featured_sections_categories[0]->getFirstMediaUrlOrDefault(CATEGORY_PATH)['title']}}" />
                        </a>
                        <div class="des_block_cat_new">
                            <h3>{{$featured_sections_categories[0]->name}}</h3>
                            <a href="{{route('front.category', [generatedNestedSlug($featured_sections_categories[0]->getRelation('ancestors')->pluck('slug')->toArray(), $featured_sections_categories[0]->slug)])}}" class="btn btn-primary transition-3d-hover rounded-lg font-weight-normal py-2 px-md-7 px-3 font-size-16 animated fadeInUp">{{__('general.view_all')}}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <ul class="row list-unstyled products-group no-gutters mb-0">
                        @foreach($featured_sections_categories[0]->frontProducts(8) as $featured_sections_product_0)
                        <li class="col-6 col-md-4 col-wd-3 product-item">
                            <div class="product-item__outer h-100">
                                <div class="product-item__inner bg-white p-3">
                                    <div class="product-item__body pb-xl-2">
                                        <div class="mb-2">
                                            <a href="{{route('front.category', [generatedNestedSlug($featured_sections_product_0->category->getRelation('ancestors')->pluck('slug')->toArray(), $featured_sections_product_0->category->slug)])}}" class="font-size-12 text-gray-5">{{$featured_sections_product_0->category->name}}</a>
                                        </div>
                                        <h5 class="mb-1 product-item__title">
                                            <a href="{{route('front.product', [generatedNestedSlug($featured_sections_product_0->category->getRelation('ancestors')->pluck('slug')->toArray(), $featured_sections_product_0->category->slug), $featured_sections_product_0->slug])}}" class="text-blue font-weight-bold">
                                                {{ Str::limit($featured_sections_product_0->name, 65) }}
                                            </a>
                                        </h5>
                                        <div class="mb-2">
                                            <a href="{{route('front.product', [generatedNestedSlug($featured_sections_product_0->category->getRelation('ancestors')->pluck('slug')->toArray(), $featured_sections_product_0->category->slug), $featured_sections_product_0->slug])}}" class="d-block text-center">
                                                <img loading="lazy" class="img-fluid"
                                                     src="{{$featured_sections_product_0->getFirstMediaUrlOrDefault(PRODUCT_PATH, 'size_150_150')['url']}}"
                                                     alt="{{$featured_sections_product_0->getFirstMediaUrlOrDefault(PRODUCT_PATH)['alt']}}"
                                                     title="{{$featured_sections_product_0->getFirstMediaUrlOrDefault(PRODUCT_PATH)['title']}}" />
                                            </a>
                                        </div>
                                        <div class="product-price">
                                            @if($featured_sections_product_0->type == 'product' && (!is_company()) && $featured_sections_product_0->price)
                                                <div class="text-gray-100">{{getCurrency('symbol')}} {{$featured_sections_product_0->formatted_price}}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(count($top_categories))
    <div class="container st_new">
        <div class="d-flex justify-content-between border-bottom border-color-1 flex-lg-nowrap flex-wrap border-md-down-top-0 border-md-down-bottom-0 mb-3 rtl">
            <h2 class="section-title section-title__full mb-0 pb-2 font-size-22">{{ __('homee.top_categories') }}</h2>
        </div>
        <div class="mb-6">
            <div class="row rtl flex-nowrap flex-md-wrap overflow-auto overflow-md-visble">
                @foreach($top_categories as $t_category)
                <div class="col-md-6 col-xl-4 mb-5 flex-shrink-0 flex-md-shrink-1">
                    <div class="bg-gray-1 overflow-hidden shadow-on-hover h-100 d-flex align-items-center">
                        <a href="{{route('front.category', [generatedNestedSlug($t_category->getRelation('ancestors')->pluck('slug')->toArray(), $t_category->slug)])}}" class="d-block">
                            <div class="media align-items-center">
                                <div class="max-width-150 img_cat_home">
                                    <img loading="lazy" class="img-fluid" src="{{$t_category->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_150_150')['url']}}" alt="{{$t_category->getFirstMediaUrlOrDefault(CATEGORY_PATH)['alt']}}" title="{{$t_category->getFirstMediaUrlOrDefault(CATEGORY_PATH)['title']}}" />
                                </div>
                                <div class="ml-4 media-body">
                                    <h3 class="mb-0 text-gray-90">{{$t_category->name}}</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if(count($featured_sections_categories) > 1)
        @foreach($featured_sections_categories->skip(1) as $featured_sections_category)
            <div class="container mb-8 st_new">
                <div class="d-flex justify-content-between border-bottom border-color-1 flex-lg-nowrap flex-wrap border-md-down-top-0 border-md-down-bottom-0 mb-3 rtl">
                    <h3 class="section-title section-title__full mb-0 pb-2 font-size-22">{{$featured_sections_category->name}}</h3>
                    <a class="d-block text-gray-16" href="{{route('front.category', [generatedNestedSlug($featured_sections_category->getRelation('ancestors')->pluck('slug')->toArray(), $featured_sections_category->slug)])}}">
                        {{__('general.go_to_all_products')}}
                        <i class="ec ec-arrow-right-categproes"></i>
                    </a>
                </div>

                <div class="row rtl">
                    <div class="col-12 col-md-2">
                        <a href="{{route('front.category', [generatedNestedSlug($featured_sections_category->getRelation('ancestors')->pluck('slug')->toArray(), $featured_sections_category->slug)])}}" class="d-block">
                            @desktop
                                <img loading="lazy" class="img-fluid img_main_block" width="200"
                                     src="{{$featured_sections_category->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_200_300')['url']}}"
                                     alt="{{$featured_sections_category->getFirstMediaUrlOrDefault(CATEGORY_PATH)['alt']}}"
                                     title="{{$featured_sections_category->getFirstMediaUrlOrDefault(CATEGORY_PATH)['title']}}" />
                            @enddesktop
                            @handheld
                                <img loading="lazy" class="img-fluid img_main_block" width="200"
                                     src="{{$featured_sections_category->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_255_364')['url']}}"
                                     alt="{{$featured_sections_category->getFirstMediaUrlOrDefault(CATEGORY_PATH)['alt']}}"
                                     title="{{$featured_sections_category->getFirstMediaUrlOrDefault(CATEGORY_PATH)['title']}}" />
                            @endhandheld
                        </a>
                    </div>
                    <div class="col-12 col-md-10 pl-md-0">
                        <!-- Tab Content -->
                        <ul class="row list-unstyled products-group no-gutters">
                            @foreach($featured_sections_category->frontProducts(6) as $featured_sections_product)
                            <li class="col-6 col-md-2 col-xl-2 product-item">
                                <div class="product-item__outer h-100">
                                    <div class="product-item__inner px-xl-3 p-3">
                                        <div class="product-item__body pb-xl-2">
                                            <div class="mb-2">
                                                <a href="{{route('front.category', [generatedNestedSlug($featured_sections_category->getRelation('ancestors')->pluck('slug')->toArray(), $featured_sections_category->slug)])}}" class="font-size-12 text-gray-5">{{$featured_sections_category->name}}</a>
                                            </div>
                                            <h5 class="mb-1 product-item__title">
                                                <a href="{{route('front.product', [generatedNestedSlug($featured_sections_product->category->getRelation('ancestors')->pluck('slug')->toArray(), $featured_sections_product->category->slug), $featured_sections_product->slug])}}" class="text-blue font-weight-bold">
                                                    {{ Str::limit($featured_sections_product->name, 65) }}
                                                </a>
                                            </h5>
                                            <div class="mb-2">
                                                <a href="{{route('front.product', [generatedNestedSlug($featured_sections_product->category->getRelation('ancestors')->pluck('slug')->toArray(), $featured_sections_product->category->slug), $featured_sections_product->slug])}}" class="d-block text-center">
                                                    <img loading="lazy" class="img-fluid"
                                                         src="{{$featured_sections_product->getFirstMediaUrlOrDefault(PRODUCT_PATH, 'size_150_150')['url']}}"
                                                         alt="{{$featured_sections_product->getFirstMediaUrlOrDefault(PRODUCT_PATH)['alt']}}"
                                                         title="{{$featured_sections_product->getFirstMediaUrlOrDefault(PRODUCT_PATH)['title']}}" />
                                                </a>
                                            </div>
                                            <div class="product-price">
                                                @if($featured_sections_product->type == 'product' && (!is_company()) && $featured_sections_product->price)
                                                    <div class="text-gray-100">{{getCurrency('symbol')}} {{$featured_sections_product->formatted_price}}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
@section('extraScripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('ready', function () {
            function filterResults() {
                let href = '?';
                if ($('#city_select').val()) {
                    href += '&filter[city]=' + $('#city_select').val();
                }
                document.location.href = href;
            }

            $(document).on('click', '#city_submit', function () {
                filterResults();
            })
        });
    });
</script>
@endsection

