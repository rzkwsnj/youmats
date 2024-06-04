@extends('front.layouts.master')
@section('metaTags')
    <title>{{__('keywordsTags.before_meta_title') . ' ' . $keyword . ' ' . __('keywordsTags.after_meta_title')}}</title>
    <meta name="description" content="{{$keyword}}">
    <meta name="keywords" content="">
    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="{{__('keywordsTags.before_meta_title') . ' ' . $keyword . ' ' . __('keywordsTags.after_meta_title')}}" />
    <meta property="og:description" content="{{$keyword}}" />
    <meta property="og:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="{{__('keywordsTags.before_meta_title') . ' ' . $keyword . ' ' . __('keywordsTags.after_meta_title')}}">
    <meta name="twitter:description" content="{{$keyword}}">
    <meta name="twitter:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}">

    <link rel="canonical" href="{{url()->current()}}" />
@endsection
@section('content')
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <!-- breadcrumb -->
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{route('home')}}">{{__('general.home')}}</a></li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">{{$keyword}}</li>
                    </ol>
                </nav>
            </div>
            <!-- End breadcrumb -->
        </div>
    </div>
    <div class="mb-6 bg-md-transparent py-6">
        <div class="container mb-8">
            <div class="d-flex justify-content-between border-bottom border-color-1 flex-lg-nowrap flex-wrap border-md-down-top-0 border-md-down-bottom-0 mb-3 rtl">
                <h1 class="section-title section-title__full mb-0 pb-2 font-size-22">{{$keyword}}</h1>
            </div>
        </div>
        <div class="container">
            <div class="row mb-8">
                <div class="col-md-12">
                    @if(count($products))
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
                    @else
                        <p class="alert alert-warning alert-block w-100">{{__('general.no_data')}}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
