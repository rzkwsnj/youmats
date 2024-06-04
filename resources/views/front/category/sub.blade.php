@extends('front.layouts.master')
@section('metaTags')
    <title>{{ getMetaTag($category, 'meta_title', $category->title) }}</title>
    <meta name="description"
        content="{{ getMetaTag($category, 'meta_desc', strip_tags((string) $category->short_desc), '', nova_get_setting_translate('categories_additional_word'), strip_tags((string) $category->name), __('general.youmats_slogon')) }}">
    <meta name="keywords" content="{{ getMetaTag($category, 'meta_keywords', '') }}">

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title"
        content="{{ getMetaTag($category, 'meta_title', $category->title . ' | ' . nova_get_setting_translate('site_name')) }}" />
    <meta property="og:description"
        content="{{ getMetaTag($category, 'meta_desc', strip_tags((string) $category->short_desc), '', nova_get_setting_translate('categories_additional_word')) }}" />
    <meta property="og:image" content="{{ $category->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_350_350')['url'] }}" />

    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title"
        content="{{ getMetaTag($category, 'meta_title', $category->title . ' | ' . nova_get_setting_translate('site_name')) }}">
    <meta name="twitter:description"
        content="{{ getMetaTag($category, 'meta_desc', strip_tags((string) $category->short_desc), '', nova_get_setting_translate('categories_additional_word')) }}">
    <meta name="twitter:image" content="{{ $category->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_350_350')['url'] }}">

    <link rel="canonical" href="{{ url()->current() }}" />

    {!! $category->getTranslation('schema', LaravelLocalization::getCurrentLocale(), false) !!}
@endsection
@section('extraStyles')
    <style>
        .btn-custom-filter {
            padding: 7px 3px;
            margin: auto 4px;
            border-radius: 19px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            background-color: #F5F5F5 !important;
            border-color: #F5F5F5 !important;
            color: #000;
            box-shadow: 0 2px 6px 2px rgba(0, 0, 0, .1);
        }

        .btn-custom-filter:hover {
            color: #000;
        }
    </style>
@endsection
@section('content')
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble" itemscope
                        itemtype="https://schema.org/BreadcrumbList">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1" itemprop="itemListElement" itemscope
                            itemtype="https://schema.org/ListItem">
                            <a itemprop="item" href="{{ route('home') }}"><span
                                    itemprop="name">{{ __('general.home') }}</span></a>
                            <meta itemprop="position" content="1" />
                        </li>
                        @foreach ($category->ancestors as $ancestor)
                            <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1" itemprop="itemListElement" itemscope
                                itemtype="https://schema.org/ListItem">
                                <a itemprop="item"
                                    href="{{ route('front.category', [generatedNestedSlug($ancestor->getRelation('ancestors')->pluck('slug')->toArray(), $ancestor->slug)]) }}"><span
                                        itemprop="name">{{ $ancestor->name }}</span></a>
                                <meta itemprop="position" content="{{ $loop->iteration + 1 }}" />
                            </li>
                        @endforeach
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
                            class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page"><span
                                itemprop="name">{{ $category->name }}</span>
                            <meta itemprop="position" content="{{ count($category->ancestors) + 2 }}" />
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if (count($subscribeVendors))
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="position-relative" style="direction: ltr">
                        <div class="js-slick-carousel u-slick u-slick--gutters-0 position-static overflow-hidden u-slick-overflow-visble pb-5 pt-2 px-1"
                            data-pagi-classes="text-center right-0 bottom-1 left-0 u-slick__pagination u-slick__pagination--long mb-0 z-index-n1 mt-3 pt-1"
                            data-slides-show="6" data-slides-scroll="1"
                            data-responsive='[{"breakpoint": 1400,"settings": {"slidesToShow": 5}}, {"breakpoint": 1200,"settings": {"slidesToShow": 3}}, {"breakpoint": 992,"settings": {"slidesToShow": 2}}, {"breakpoint": 768,"settings": {"slidesToShow": 2}}, {"breakpoint": 554,"settings": {"slidesToShow": 2}}]'>
                            @foreach ($subscribeVendors as $subscribeVendor)
                                <div class="text-center js-slide products-group img-logos-new">
                                    <div>
                                        <a href="{{ route('vendor.show', [$subscribeVendor->slug]) }}"
                                            class="d-block text-center">
                                            <img class="img-fluid img-logos-new"
                                                style="display: inline-block !important;height: 50px !important;"
                                                src="{{ $subscribeVendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_height_50')['url'] }}"
                                                alt="{{ $subscribeVendor->getFirstMediaUrlOrDefault(VENDOR_LOGO)['alt'] }}"
                                                title="{{ $subscribeVendor->getFirstMediaUrlOrDefault(VENDOR_LOGO)['title'] }}">
                                        </a>
                                        <p class="text-gray-100">{{ $subscribeVendor->name }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-6 bg-md-transparent">
        @if ($category->getTranslation('title', app()->getLocale(), false))
            <div class="container mb-8">
                <div
                    class="d-flex justify-content-between border-bottom border-color-1 flex-lg-nowrap flex-wrap border-md-down-top-0 border-md-down-bottom-0 mb-3 rtl">
                    <h1 class="section-title section-title__full mb-0 pb-2 font-size-22">
                        {{ $category->getTranslation('title', app()->getLocale(), false) }}</h1>
                </div>
                @if ($category->getTranslation('desc', app()->getLocale(), false))
                    <div class="d-block d-lg-none d-xl-none text-left">
                        {!! $category->getTranslation('desc', app()->getLocale(), false) !!}
                    </div>
                @endif
            </div>
        @endif
        <form method="get" action="{{ url()->current() }}">
            <div class="container">
                <div class="row mb-8 rtl">

                    <div class="col-xl-3 col-wd-2gdot5 d-none d-lg-block d-xl-block">
                        @if (!is_company() && $maxPrice > 0)
                            <div class="mb-6">
                                <div class="range-slider bg-gray-3 p-3">
                                    <h4 class="font-size-14 mb-3 font-weight-bold">{{ __('general.price') }}</h4>
                                    <!-- Range Slider -->
                                    <input class="js-range-slider" type="text" id="price_range"
                                        data-extra-classes="u-range-slider u-range-slider-indicator u-range-slider-grid"
                                        data-type="double" data-grid="false" data-hide-from-to="true"
                                        data-prefix="{{ getCurrency('symbol') }}" data-min="{{ $minPrice }}"
                                        data-max="{{ $maxPrice }}"
                                        data-from="{{ explode(';', request()->input('filter.price'))[0] ?? $minPrice }}"
                                        data-to="{{ explode(';', request()->input('filter.price'))[1] ?? $maxPrice }}"
                                        name="price" data-result-min="#rangeSliderExample3MinResultCategory"
                                        data-result-max="#rangeSliderExample3MaxResultCategory">
                                    <!-- End Range Slider -->
                                    <div class="mt-1 text-gray-111 d-flex mb-4">
                                        <span class="mr-0dot5">{{ __('general.price') }}: </span>
                                        <span>{{ getCurrency('symbol') }} </span>
                                        <span id="rangeSliderExample3MinResultCategory">{{ $minPrice }}</span>
                                        <span class="mx-0dot5"> — </span>
                                        <span>{{ getCurrency('symbol') }} </span>
                                        <span id="rangeSliderExample3MaxResultCategory">{{ $maxPrice }}</span>
                                    </div>
                                    <button class="btn px-4 btn-primary-dark-w py-2 rounded-lg text-white" type="button"
                                        id="priceFilterBtn">{{ __('general.search_button') }}</button>
                                </div>
                            </div>
                        @endif
                        @foreach ($category->attributes as $attribute)
                            <div class="mb-6">
                                <div class="border-bottom border-color-1 mb-5">
                                    <h3 class="section-title section-title__sm mb-0 pb-2 font-size-18">
                                        {{ $attribute->key }}</h3>
                                </div>
                                <div class="border-bottom mb-4 attr-container">
                                    @foreach ($attribute->values as $value)
                                        <div
                                            class="form-group d-flex align-items-center justify-content-between mb-2 pb-1">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input filter-checkboxes"
                                                    name="attributes" @if (in_array($attribute->id . '-' . $value->id, explode(',', ((string) request()->input('filter.attributes'))))) checked @endif
                                                    value="{{ $attribute->id . '-' . $value->id }}"
                                                    id="{{ $attribute->id . '_' . $value->id }}">
                                                <label class="custom-control-label"
                                                    for="{{ $attribute->id . '_' . $value->id }}">{{ $value->value }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        @if (count($siblings))
                            <div class="mb-6">
                                <div class="border-bottom border-color-1 mb-5">
                                    <h3 class="section-title section-title__sm mb-0 pb-2 font-size-18">
                                        {{ __('general.categories') }}</h3>
                                </div>
                                <div class="border-bottom mb-4 attr-container">
                                    @foreach ($siblings as $sibling)
                                        <div
                                            class="form-group d-flex align-items-center justify-content-between mb-2 pb-1">
                                            <div class="custom-control custom-checkbox">
                                                <a @if ($sibling->id == $category->id) style="font-weight: bold" @endif
                                                    href="{{ route('front.category', [generatedNestedSlug($sibling->getRelation('ancestors')->pluck('slug')->toArray(), $sibling->slug)]) }}">{{ $sibling->name }}</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if (count($tags))
                            <div class="mb-6">
                                <div class="border-bottom border-color-1 mb-5">
                                    <h3 class="section-title section-title__sm mb-0 pb-2 font-size-18">
                                        {{ __('general.search_tags') }}</h3>
                                </div>
                                <div class="border-bottom mb-4 attr-container">
                                    @foreach ($tags as $tag)
                                        <div
                                            class="form-group d-flex align-items-center justify-content-between mb-2 pb-1">
                                            <div class="custom-control custom-checkbox">
                                                <a href="{{ route('front.tag', [$tag->slug]) }}"
                                                    class="custom-control-label">{{ $tag->name }}
                                                    <span class="text-gray-25 font-size-12 font-weight-norma3">
                                                        ({{ count($tag->products) }})
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-xl-9 col-wd-9gdot5" style="width: 100%!important;">
                        @if ($category->getTranslation('desc', app()->getLocale(), false))
                            <div class="d-none d-lg-block d-xl-block text-left">
                                {!! $category->getTranslation('desc', app()->getLocale(), false) !!}
                            </div>
                        @endif
                        @if (count($category->children))
                            <div class="mb-6 bg-gray-7">
                                <div class="container">
                                    <ul class="row flex-nowrap flex-md-wrap overflow-auto overflow-md-visble rtl"
                                        style="padding-top: 15px">
                                        @foreach ($category->children as $child)
                                            <li class="col-md-4 col-lg-3 col-xl-4 col-xl-2gdot4 mb-3 flex-shrink-0 flex-md-shrink-1"
                                                style="list-style: none;">
                                                <div class="bg-white overflow-hidden shadow-on-hover d-flex align-items-center"
                                                    style="height: 100px !important;">
                                                    <a href="{{ route('front.category', [generatedNestedSlug($child->getRelation('ancestors')->pluck('slug')->toArray(), $child->slug)]) }}"
                                                        class="d-block pr-2">
                                                        <div class="media align-items-center">
                                                            <div class="pt-2">
                                                                <img loading="lazy" class="img-fluid img_category_page"
                                                                    src="{{ $child->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_85_85')['url'] }}"
                                                                    alt="{{ $child->getFirstMediaUrlOrDefault(CATEGORY_PATH)['alt'] }}"
                                                                    title="{{ $child->getFirstMediaUrlOrDefault(CATEGORY_PATH)['title'] }}">
                                                            </div>
                                                            <div class="ml-3 media-body">
                                                                <h2 class="mb-0 text-gray-90" style="font-size: 1rem;">
                                                                    {{ $child->name }}</h2>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Mobile Filter -->
                        <div class="mobile-filter col-xl-3 d-block d-lg-none d-xl-none">
                            <div class="position-relative">
                                <div class="filter-carousel js-slick-carousel u-slick u-slick--gutters-0 position-static overflow-hidden u-slick-overflow-visble pb-3 px-1"
                                    data-arrows-classes="u-slick__arrow u-slick__arrow--flat u-slick__arrow-centered--y rounded-circle adjust-filter-arrows"
                                    data-pagi-classes="text-center right-0 bottom-1 left-0 u-slick__pagination u-slick__pagination--long mb-0 z-index-n1 mt-3 pt-1"
                                    data-arrow-left-classes="fa fa-angle-left u-slick__arrow-classic-inner--left z-index-9"
                                    data-arrow-right-classes="fa fa-angle-right u-slick__arrow-classic-inner--right"
                                    data-slides-show="3" data-slides-scroll="1"
                                    data-slick='{
                            "dots": false
                            @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') ,"rtl": true @endif
                         }'
                                    data-responsive='[{"breakpoint": 768,"settings": {"slidesToShow": 3}}, {"breakpoint": 554,"settings": {"slidesToShow": 3}}]'>
                                    @foreach ($category->attributes as $attribute)
                                        <div class="text-center js-slide">
                                            <label class="btn btn-primary btn-custom-filter" data-toggle="modal"
                                                data-target=".modal_{{ $attribute->id }}">
                                                {{ $attribute->key }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @foreach ($category->attributes as $attribute)
                            <div class="modal fade modal_{{ $attribute->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg"
                                    style="position: fixed;top: auto;left: auto;right: auto;bottom: 0;margin: 0;width: 100%">
                                    <div class="modal-content" style="padding: 15px">
                                        <div class="border-bottom border-color-1 mb-5">
                                            <h3 class="section-title section-title__sm mb-0 pb-2 font-size-18">
                                                {{ $attribute->key }}</h3>
                                        </div>
                                        <div class="border-bottom mb-4 attr-container" style="padding-right: 10px">
                                            @foreach ($attribute->values as $value)
                                                <div
                                                    class="form-group d-flex align-items-center justify-content-between mb-2 pb-1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                            class="custom-control-input filter-checkboxes"
                                                            {{-- name="attributes" --}}
                                                            @if (in_array($attribute->id . '-' . $value->id, explode(',', ((string) request()->input('filter.attributes'))))) checked @endif
                                                            value="{{ $attribute->id . '-' . $value->id }}"
                                                            id="{{ $attribute->id . '_' . $value->id }}">
                                                        <label class="custom-control-label"
                                                            for="{{ $attribute->id . '_' . $value->id }}">{{ $value->value }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <!-- Mobile Filter -->

                        <div id="productsContainer">
                            @include('front.category.productsContainer', [
                                'category' => $category,
                                'products' => $products,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @include('front.layouts.partials.change_city')

    @if ($category->contact_widgets)
        <a class="js-go-to u-go-to" href="#" data-position='{"bottom": 125, "right": 15}' data-type="fixed"
            data-offset-top="400" data-compensation="#header" data-show-effect="slideInUp"
            data-hide-effect="slideOutDown">
            <span class="fas fa-arrow-up u-go-to__inner"></span>
        </a>

        @if (isset($widget_phone))
            <button class="widget log" data-log="call" type="button" onclick="SetUpCall({{ $widget_phone }})"><i
                    class="fas fa-phone"></i></button>
        @else
            <a target="_blank" class="widget log" data-log="call" href="tel:{{ nova_get_setting('widget_phone') }}"><i
                    class="fas fa-phone"></i></a>
        @endif

        <a target="_blank" class="widget whatsapp log" data-log="chat"
            href="{{ $widget_whatsapp ?? 'https://wa.me/' . nova_get_setting('widget_whatsapp') }}" target="_blank"><i
                class="fab fa-whatsapp"></i></a>
    @else
        <a class="js-go-to u-go-to" href="#" data-position='{"bottom": 15, "right": 15}' data-type="fixed"
            data-offset-top="400" data-compensation="#header" data-show-effect="slideInUp"
            data-hide-effect="slideOutDown">
            <span class="fas fa-arrow-up u-go-to__inner"></span>
        </a>
    @endif

@endsection
@section('extraScripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('ready', function() {
                function getAttributesIds(checkboxName) {
                    let checkBoxes = document.getElementsByName(checkboxName);
                    let ids = Array.prototype.slice.call(checkBoxes)
                        .filter(ch => ch.checked == true)
                        .map(ch => ch.value);
                    return ids;
                }

                function filterResults() {
                    let attributesIds = getAttributesIds("attributes");
                    let href = '?';
                    if (attributesIds.length) {
                        href += 'filter[attributes]=' + attributesIds;
                    }
                    if ($('#is_price').is(':checked')) {
                        href += '&filter[is_price]=' + $('#is_price').val();
                    }
                    if ($('#is_delivery').is(':checked')) {
                        href += '&filter[is_delivery]=' + $('#is_delivery').val();
                    }
                    if ($('#price_range').val()) {
                        href += '&filter[price]=' + $('#price_range').val();
                    }
                    if ($('#city_select').val()) {
                        href += '&filter[city]=' + $('#city_select').val();
                    }
                    if ($('#sort_select').val()) {
                        href += '&sort=' + $('#sort_select').val();
                    }

                    document.location.href = href;
                }

                $(document).on('change', '.filter-checkboxes', function() {
                    filterResults();
                });
                $(document).on('click', '#priceFilterBtn', function() {
                    filterResults();
                });
                $(document).on('click', '#city_submit', function() {
                    filterResults();
                });
                $(document).on('change', '#sort_select', function() {
                    filterResults();
                });
                $(document).on('change', '#is_price', function() {
                    filterResults();
                });
                $(document).on('change', '#is_delivery', function() {
                    filterResults();
                });


                let fixmeTop = $('.mobile-filter').offset().top,
                    headerHeight = document.querySelector('.nav_fixed').offsetHeight;

                $(window).scroll(function() {
                    let currentScroll = $(window).scrollTop();
                    if (currentScroll >= fixmeTop - (headerHeight + 55)) {
                        $('.mobile-filter').css({
                            position: 'fixed',
                            top: headerHeight + 'px',
                            right: 0,
                            backgroundColor: '#FFF',
                            zIndex: '1000',
                            padding: '15px 0 0'
                        });
                    } else {
                        $('.mobile-filter').css({
                            position: 'relative',
                            top: '0',
                            backgroundColor: 'transparent',
                            padding: '0 15px 15px'
                        });
                    }

                });

            });
        });
    </script>
@endsection
