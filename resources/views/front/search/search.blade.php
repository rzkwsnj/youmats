@extends('front.layouts.master')
@section('metaTags')
    <title>{{ $page_title . ' | ' . nova_get_setting_translate('site_name') }}</title>
    <meta name="description" content="{{ $page_title . ' | ' . nova_get_setting_translate('site_name') }}">
    <meta name="keywords" content="{{ $page_title . ' | ' . nova_get_setting_translate('site_name') }}">

    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="{{ $page_title . ' | ' . nova_get_setting_translate('site_name') }}" />
    <meta property="og:description" content="{{ $page_title . ' | ' . nova_get_setting_translate('site_name') }}" />
    <meta property="og:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}" />

    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="{{ $page_title . ' | ' . nova_get_setting_translate('site_name') }}">
    <meta name="twitter:description" content="{{ $page_title . ' | ' . nova_get_setting_translate('site_name') }}">
    <meta name="twitter:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}">

    <link rel="canonical" href="{{url()->current()}}" />
@endsection
@section('content')
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble" itemscope itemtype="https://schema.org/BreadcrumbList">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"  itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a itemprop="item" href="{{route('home')}}"><span itemprop="name">{{__('general.home')}}</span></a>
                            <meta itemprop="position" content="1" />
                        </li>
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">
                            <span itemprop="name">{{ $page_title }}</span>
                            <meta itemprop="position" content="2" />
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="mb-6 bg-md-transparent">
        <form method="get" action="{{url()->current()}}">
            <div class="container">
                <div class="row mb-8 rtl">
                    <div class="col-xl-3 col-wd-2gdot5 d-none d-lg-block d-xl-block">
                        @if(!is_company() && $maxPrice > 0)
                            <div class="mb-6">
                                <div class="range-slider bg-gray-3 p-3">
                                    <h4 class="font-size-14 mb-3 font-weight-bold">{{__('general.price')}}</h4>
                                    <!-- Range Slider -->
                                    <input class="js-range-slider" type="text" id="price_range"
                                           data-extra-classes="u-range-slider u-range-slider-indicator u-range-slider-grid"
                                           data-type="double" data-grid="false" data-hide-from-to="true" data-prefix="{{ getCurrency('symbol') }}"
                                           data-min="{{$minPrice}}" data-max="{{$maxPrice}}"
                                           data-from="{{(explode(';', request()->input('filter.price'))[0]) ?? $minPrice}}"
                                           data-to="{{(explode(';', request()->input('filter.price'))[1]) ?? $maxPrice}}" name="price"
                                           data-result-min="#rangeSliderExample3MinResultCategory"
                                           data-result-max="#rangeSliderExample3MaxResultCategory">
                                    <!-- End Range Slider -->
                                    <div class="mt-1 text-gray-111 d-flex mb-4">
                                        <span class="mr-0dot5">{{__('general.price')}}: </span>
                                        <span>{{ getCurrency('symbol') }} </span>
                                        <span id="rangeSliderExample3MinResultCategory">{{$minPrice}}</span>
                                        <span class="mx-0dot5"> — </span>
                                        <span>{{ getCurrency('symbol') }} </span>
                                        <span id="rangeSliderExample3MaxResultCategory">{{$maxPrice}}</span>
                                    </div>
                                    <button class="btn px-4 btn-primary-dark-w py-2 rounded-lg text-white" type="button" id="priceFilterBtn">{{__('general.search_button')}}</button>
                                </div>
                            </div>
                        @endif
                        @if(isset($search_categories) && count($search_categories))
                            <div class="mb-6">
                                <div class="border-bottom border-color-1 mb-5">
                                    <h3 class="section-title section-title__sm mb-0 pb-2 font-size-18">{{__('general.categories')}}</h3>
                                </div>
                                <div class="border-bottom mb-4 attr-container">
                                    @foreach($search_categories as $search_category)
                                        <div class="form-group d-flex align-items-center justify-content-between mb-2 pb-1">
                                            <div class="custom-control custom-checkbox">
                                                <a href="{{route('front.category', [generatedNestedSlug($search_category->ancestors()->pluck('slug')->toArray(), $search_category->slug)])}}">
                                                    {{$search_category->name}}
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if(isset($search_tags) && count($search_tags))
                            <div class="mb-6">
                                <div class="border-bottom border-color-1 mb-5">
                                    <h3 class="section-title section-title__sm mb-0 pb-2 font-size-18">{{__('general.search_tags')}}</h3>
                                </div>
                                <div class="border-bottom mb-4 attr-container">
                                    @foreach($search_tags as $search_tag)
                                        <div class="form-group d-flex align-items-center justify-content-between mb-2 pb-1">
                                            <div class="custom-control custom-checkbox">
                                                <a href="{{route('front.tag', [$search_tag->slug])}}" class="custom-control-label">
                                                    {{$search_tag->name}}
{{--                                                    <span class="text-gray-25 font-size-12 font-weight-norma3"> ({{count($search_tag->products)}})</span>--}}
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-xl-9 col-wd-9gdot5" style="width: 100%!important;">
                        <div id="productsContainer">
                            @include('front.category.productsContainer', ['category' => null, 'products' => $search_products])
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @if(is_individual())
        @include('front.layouts.partials.change_city')
    @endif
@endsection
@section('extraScripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('ready', function () {
                function filterResults() {
                    let href = '?filter[name]={{ $page_title }}';
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

                $(document).on('click', '#priceFilterBtn', function () {
                    filterResults();
                });
                $(document).on('click', '#city_submit', function () {
                    filterResults();
                });
                $(document).on('change', '#sort_select', function () {
                    filterResults();
                });
                $(document).on('change', '#is_price', function () {
                    filterResults();
                });
                $(document).on('change', '#is_delivery', function () {
                    filterResults();
                });

            });
        });
    </script>
@endsection
