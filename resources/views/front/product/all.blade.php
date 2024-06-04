@extends('front.layouts.master')
@section('metaTags')
    <title>
        @if (App::isLocale('en'))
            All Building Materials Products in Saudi Arabia - YouMats Building Materials
        @else
            جميع منتجات مواد البناء فى السعوديه – يوماتس لمود البناء
        @endif
    </title>
    <meta name="description"
        @if (App::isLocale('en')) content="Shop all building and construction materials products in Saudi Arabia online through YouMats. The store provides all factories and suppliers of building materials at the best prices."
        @else
            content="تسوق جميع منتجات مود البناء فى السعوديه من خلال موقع يوماتس لمود البناء والتشييد إذ يتيح لك التعامل مع أفضل موردين مستلزمات مواد البناءداخل السعودية." @endif>
    <meta name="keywords" content="">
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title"
        @if (App::isLocale('en')) content="All Building Materials Products in Saudi Arabia - YouMats Building Materials"
        @else
        content="جميع منتجات مواد البناء فى السعوديه – يوماتس لمود البناء" @endif />
    <meta property="og:description"
        @if (App::isLocale('en')) content="Shop all building and construction materials products in Saudi Arabia online through YouMats. The store provides all factories and suppliers of building materials at the best prices."
        @else
            content="تسوق جميع منتجات مود البناء فى السعوديه من خلال موقع يوماتس لمود البناء والتشييد إذ يتيح لك التعامل مع أفضل موردين مستلزمات مواد البناءداخل السعودية." @endif />
    <meta property="og:image" content="" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="">
    <meta name="twitter:description" content="">
    <meta name="twitter:image" content="">
    <link rel="canonical" href="{{ url()->current() }}" />
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
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
                            class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page"><span
                                itemprop="name">{{ __('product.all_products') }}</span>
                            <meta itemprop="position" content="2" />
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="mb-6 bg-md-transparent py-6">
        <div class="container">
            <div class="row mb-8">
                <div class="col-xl-12">

                    <div class="d-block d-md-flex flex-center-between mb-3 rtl">
                        <h3 class="font-size-25 mb-2 mb-md-0">{{ __('product.all_products') }}</h3>
                        <p class="font-size-14 text-gray-90 mb-0">{{ __('general.showing') }}
                            {{ $products->firstItem() }}–{{ $products->lastItem() }} {{ __('general.of') }}
                            {{ $products->total() }} {{ __('general.results') }}</p>
                    </div>

                    <!-- Shop-control-bar -->
                    <div class="bg-gray-1 flex-center-between borders-radius-9 py-1 rtl">
                        <div class="px-3 d-none d-xl-block">
                            <ul class="nav nav-tab-shop" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="grid-view-tab" data-toggle="pill" href="#grid-view"
                                        role="tab" aria-controls="grid-view" aria-selected="false">
                                        <div class="d-md-flex justify-content-md-center align-items-md-center">
                                            <i class="fa fa-th"></i>
                                        </div>
                                    </a>
                                </li>
                                {{--                                <li class="nav-item"> --}}
                                {{--                                    <a class="nav-link" id="list-view-tab" data-toggle="pill" href="#list-view" role="tab" aria-controls="list-view" aria-selected="true"> --}}
                                {{--                                        <div class="d-md-flex justify-content-md-center align-items-md-center"> --}}
                                {{--                                            <i class="fa fa-th-list"></i> --}}
                                {{--                                        </div> --}}
                                {{--                                    </a> --}}
                                {{--                                </li> --}}
                            </ul>
                        </div>
                        <div class="rtl">
                            {{ __('general.city_location_text') }}: {{ getCurrentCityName() }}
                            <button type="button" class="choose_city btn btn-primary btn-xs" data-toggle="modal"
                                data-target=".change_city_modal">{{ __('general.change_city_button') }}</button>
                            {{ __('general.category_word_after_change_city_button') }}
                        </div>
                        <nav class="px-3 flex-horizontal-center text-gray-20 d-none d-xl-flex">
                            <a class="text-gray-30 font-size-20 mr-2" href="{{ $products->previousPageUrl() }}">
                                @if (app()->getLocale() == 'ar')
                                    &nbsp;→&nbsp;
                                @else
                                    &nbsp;←&nbsp;
                                @endif
                            </a>
                            <b>{{ $products->currentPage() }} </b> &nbsp; {{ __('general.of') }}
                            {{ $products->lastPage() }}
                            <a class="text-gray-30 font-size-20 ml-2" href="{{ $products->nextPageUrl() }}">
                                @if (app()->getLocale() == 'ar')
                                    &nbsp;←&nbsp;
                                @else
                                    &nbsp;→&nbsp;
                                @endif
                            </a>
                        </nav>
                    </div>
                    <!-- End Shop-control-bar -->

                    <!-- Tab Content -->
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade pt-2 show active" id="grid-view" role="tabpanel"
                            aria-labelledby="grid-view-tab" data-target-group="groups">
                            <ul class="row list-unstyled products-group no-gutters">
                                @foreach ($products as $product)
                                    @if (isset($product->category))
                                        <li class="col-6 col-md-3 col-wd-2gdot4 product-item st_new">
                                            @include('front.layouts.partials.product_box', [
                                                'product' => $product,
                                                'view' => 'grid',
                                            ])
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        {{--                        <div class="tab-pane fade pt-2" id="list-view" role="tabpanel" aria-labelledby="list-view-tab" data-target-group="groups"> --}}
                        {{--                            <ul class="d-block list-unstyled products-group prodcut-list-view-small"> --}}
                        {{--                                @foreach ($products as $product) --}}
                        {{--                                    @if (isset($product->category)) --}}
                        {{--                                    @include('front.layouts.partials.product_box', ['product' => $product, 'view' => 'list']) --}}
                        {{--                                    @endif --}}
                        {{--                                @endforeach --}}
                        {{--                            </ul> --}}
                        {{--                        </div> --}}
                    </div>
                    <!-- End Tab Content -->
                    <nav class="d-md-flex justify-content-between align-items-center border-top pt-3"
                        aria-label="Page navigation example">
                        <ul class="pagination mb-0 pagination-shop justify-content-center justify-content-md-start">
                            {{ $products->onEachSide(0)->links() }}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    @include('front.layouts.partials.change_city')
@endsection
@section('extraScripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('ready', function() {
                function filterResults() {
                    let href = '?';
                    if ($('#city_select').val()) {
                        href += '&filter[city]=' + $('#city_select').val();
                    }
                    document.location.href = href;
                }

                $(document).on('click', '#city_submit', function() {
                    filterResults();
                })
            });
        });
    </script>
@endsection
