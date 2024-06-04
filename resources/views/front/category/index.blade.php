@extends('front.layouts.master')
@section('metaTags')
    <title>{{getMetaTag($category, 'meta_title', $category->title)}}</title>
    <meta name="description" content="{{ getMetaTag($category, 'meta_desc', strip_tags($category->short_desc), '', nova_get_setting_translate('categories_additional_word'), strip_tags($category->name), __('general.youmats_slogon') ) }}">
    <meta name="keywords" content="{{getMetaTag($category, 'meta_keywords', '')}}">

    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="{{getMetaTag($category, 'meta_title', $category->title . ' | ' . nova_get_setting_translate('site_name'))}}" />
    <meta property="og:description" content="{{ getMetaTag($category, 'meta_desc', strip_tags($category->short_desc), '', nova_get_setting_translate('categories_additional_word')) }}" />
    <meta property="og:image" content="{{$category->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_350_350')['url']}}" />

    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="{{getMetaTag($category, 'meta_title', $category->title . ' | ' . nova_get_setting_translate('site_name'))}}">
    <meta name="twitter:description" content="{{ getMetaTag($category, 'meta_desc', strip_tags($category->short_desc), '', nova_get_setting_translate('categories_additional_word')) }}">
    <meta name="twitter:image" content="{{$category->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_350_350')['url']}}">

    <link rel="canonical" href="{{url()->current()}}" />

    {!! $category->getTranslation('schema', LaravelLocalization::getCurrentLocale(), false) !!}
@endsection
@section('extraScripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('ready', function () {
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

            $(document).on('change', '.filter-checkboxes', function () {
                filterResults();
            });
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


            let fixmeTop = $('.mobile-filter').offset().top,
                headerHeight = document.querySelector('.nav_fixed').offsetHeight;

            $(window).scroll(function() {
                let currentScroll = $(window).scrollTop();
                if (currentScroll >= fixmeTop-(headerHeight+55)) {
                    $('.mobile-filter').css({
                        position: 'fixed',
                        top: headerHeight+'px',
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
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page"><span itemprop="name">{{$category->name}}</span>
                            <meta itemprop="position" content="2" />
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- End breadcrumb -->
        </div>
    </div>
    <div class="mb-6 bg-gray-7 py-6">
        @if($category->getTranslation('title', app()->getLocale(), false))
        <div class="container mb-8">
            <div class="d-flex justify-content-between border-bottom border-color-1 flex-lg-nowrap flex-wrap border-md-down-top-0 border-md-down-bottom-0 mb-3 rtl">
                <h1 class="section-title section-title__full mb-0 pb-2 font-size-22">{{$category->getTranslation('title', app()->getLocale(), false)}}</h1>
            </div>
        </div>
        @endif
        <div class="container">
            <ul class="row flex-nowrap flex-md-wrap overflow-auto overflow-md-visble rtl">
                @foreach($children as $child)
                <li class="col-md-4 col-lg-3 col-xl-4 col-xl-2gdot4 mb-3 flex-shrink-0 flex-md-shrink-1" style="list-style: none;">
                    <div class="bg-white overflow-hidden shadow-on-hover h-100 d-flex align-items-center">
                        <a href="{{route('front.category', [generatedNestedSlug($child->getRelation('ancestors')->pluck('slug')->toArray(), $child->slug)])}}" class="d-block pr-2 pr-wd-6">
                            <div class="media align-items-center">
                                <div>
                                    <img loading="lazy" class="img-fluid img_category_page" src="{{$child->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_100_100')['url']}}" alt="{{$child->getFirstMediaUrlOrDefault(CATEGORY_PATH)['alt']}}" title="{{$child->getFirstMediaUrlOrDefault(CATEGORY_PATH)['title']}}">
                                </div>
                                <div class="ml-3 media-body">
                                    <h2 class="mb-0 text-gray-90" style="font-size: 1rem;">{{$child->name}}</h2>
                                </div>
                            </div>
                        </a>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="mb-6 bg-md-transparent py-0">
        <div class="container">
            <div class="row mb-8">
                <div class="col-xl-12">
                    <div id="productsContainer">
                        @include('front.category.productsContainer', ['category' => $category, 'products' => $products])
                    </div>
                </div>
            </div>
        </div>
    </div>
        @include('front.layouts.partials.change_city')

    @if($category->contact_widgets)
        <a class="js-go-to u-go-to" href="#" data-position='{"bottom": 125, "right": 15}' data-type="fixed" data-offset-top="400" data-compensation="#header" data-show-effect="slideInUp" data-hide-effect="slideOutDown">
            <span class="fas fa-arrow-up u-go-to__inner"></span>
        </a>

        @if(isset($widget_phone))
            <button class="widget log" data-log="call" type="button" onclick="SetUpCall({{$widget_phone}})"><i class="fas fa-phone"></i></button>
        @else
            <a target="_blank" class="widget log" data-log="call" href="tel:{{ nova_get_setting('widget_phone')}}"><i class="fas fa-phone"></i></a>
        @endif

        <a target="_blank" class="widget whatsapp log" data-log="chat" href="{{$widget_whatsapp ?? 'https://wa.me/' . nova_get_setting('widget_whatsapp')}}" target="_blank"><i class="fab fa-whatsapp"></i></a>
    @else
        <a class="js-go-to u-go-to" href="#" data-position='{"bottom": 15, "right": 15}' data-type="fixed" data-offset-top="400" data-compensation="#header" data-show-effect="slideInUp" data-hide-effect="slideOutDown">
            <span class="fas fa-arrow-up u-go-to__inner"></span>
        </a>
    @endif

@endsection
