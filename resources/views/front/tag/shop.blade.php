@extends('front.layouts.master')
@section('metaTags')
    <title>{{ __('shop_page.meta_title') }}</title>
    <meta name="description" content="{{ __('shop_page.meta_desc') }}">
    <meta name="keywords" content="{{ __('shop_page.meta_keywords') }}">

    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="{{ __('shop_page.meta_title') }}" />
    <meta property="og:description" content="{{ __('shop_page.meta_desc') }}" />
    <meta property="og:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}" />

    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="{{ __('shop_page.meta_title') }}">
    <meta name="twitter:description" content="{{ __('shop_page.meta_desc') }}">
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
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page"><span itemprop="name">{{ __('shop_page.title') }}</span>
                            <meta itemprop="position" content="2" />
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="mb-6 bg-gray-7 py-6">
        <div class="container">
            <div class="row rtl">
                @foreach($keywords as $slug => $keyword)
                    <div class="col-md-4 col-lg-3 col-xl-4 col-xl-2gdot4 mb-3">
                        <div class="bg-white shadow-on-hover" style="padding: 15px 0">
                            <a href="{{route('front.tag.search', [$slug])}}" class="d-block text-center">
                                <span class="text-gray-90" style="font-size: 1rem;">{{$keyword}}</span>
                            </a>
                        </div>
                    </div>
                @endforeach
                <div class="col-md-12 text-center">
                    <nav class="rtl border-top pt-3" aria-label="Page navigation example">
                        <ul class="pagination mb-0 pagination-shop">
                            {{$keywords->onEachSide(2)->links()}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>


@endsection
