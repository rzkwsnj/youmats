@extends('front.layouts.master')
@section('metaTags')
    <title>{{$tag->meta_title}}</title>
    <meta name="description" content="{{ getMetaTag('', '', $tag->meta_desc, '', '', $tag->name, __('general.youmats_slogon')) }}">
    <meta name="keywords" content="{{$tag->meta_keywords}}">
    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="{{$tag->meta_title}}" />
    <meta property="og:description" content="{{ getMetaTag('', '', $tag->meta_desc, '', '', $tag->name, __('general.youmats_slogon') ) }}" />
    <meta property="og:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="{{$tag->meta_title}}">
    <meta name="twitter:description" content="{{ getMetaTag('', '', $tag->meta_desc, '', '', $tag->name, __('general.youmats_slogon') ) }}">
    <meta name="twitter:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}">

    <link rel="canonical" href="{{url()->current()}}" />

    {!! $tag->schema !!}
@endsection
@section('content')
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <!-- breadcrumb -->
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{route('home')}}">{{__('general.home')}}</a></li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">{{$tag->name}}</li>
                    </ol>
                </nav>
            </div>
            <!-- End breadcrumb -->
        </div>
    </div>
    <div class="mb-6 bg-md-transparent py-6">
        @if($tag->getTranslation('name', app()->getLocale(), false))
            <div class="container mb-8">
                <div class="d-flex justify-content-between border-bottom border-color-1 flex-lg-nowrap flex-wrap border-md-down-top-0 border-md-down-bottom-0 mb-3 rtl">
                    <h1 class="section-title section-title__full mb-0 pb-2 font-size-22">{{$tag->getTranslation('name', app()->getLocale(), false)}}</h1>
                </div>
                @if($tag->getTranslation('desc', app()->getLocale(), false))
                    <div class="d-block d-lg-none d-xl-none text-left">
                        {!! $tag->getTranslation('desc', app()->getLocale(), false) !!}
                    </div>
                @endif
            </div>
        @endif
        <div class="container">
            <div class="row mb-8">

                <div class="d-none d-xl-block col-xl-3 col-wd-2gdot5">
                    <div class="mb-6">
                        <div class="border-bottom border-color-1 mb-5">
                            <h3 class="section-title section-title__sm mb-0 pb-2 font-size-18">{{ __('general.related_tags') }}</h3>
                        </div>
                        <div class="border-bottom pb-4 mb-4">
                            @foreach($tags as $row)
                                <div class="form-group d-flex align-items-center justify-content-between mb-2 pb-1">
                                    <div class="custom-control custom-checkbox">
                                        <a @if($row->id == $tag->id) style="font-weight: bold" @endif
                                        href="{{route('front.tag', [$row->slug])}}" class="custom-control-label">{{$row->name}}
                                            <span class="text-gray-25 font-size-12 font-weight-norma3"> ({{count($row->products)}})</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-wd-9gdot5">
                    @if($tag->getTranslation('desc', app()->getLocale(), false))
                        <div class="d-none d-lg-block d-xl-block text-left">
                            {!! $tag->getTranslation('desc', app()->getLocale(), false) !!}
                        </div>
                    @endif

                    <div class="d-block d-md-flex flex-center-between mb-3">
                        <p class="font-size-14 text-gray-90 mb-0">{{__('general.showing')}} {{$products->firstItem()}}–{{$products->firstItem() + count($products->items()) -1}} {{__('general.of')}} {{$products->total()}} {{__('general.results')}}</p>
                    </div>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade pt-2 show active" id="grid-view" role="tabpanel" aria-labelledby="grid-view-tab" data-target-group="groups">
                            <ul class="row list-unstyled products-group no-gutters">
                                @foreach($products as $product)
                                    <li class="col-6 col-md-3 col-wd-2gdot4 product-item">
                                        @include('front.layouts.partials.product_box', ['product' => $product, 'view' => 'grid'])
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <nav class="d-md-flex justify-content-between align-items-center border-top pt-3" aria-label="Page navigation example">
                        <ul class="pagination mb-0 pagination-shop justify-content-center justify-content-md-start">
                            {{$products->onEachSide(0)->links()}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection
