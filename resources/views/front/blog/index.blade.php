@extends('front.layouts.master')
@section('metaTags')
    <title>{{ (json_decode(nova_get_setting('blog_home_meta_title'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}</title>
    <meta name="keywords" content="{{ (json_decode(nova_get_setting('blog_home_meta_keywords'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}">
    <meta name="description" content="{{ (json_decode(nova_get_setting('blog_home_meta_desc'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}">
    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="{{ (json_decode(nova_get_setting('blog_home_meta_title'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}" />
    <meta property="og:description" content="{{ (json_decode(nova_get_setting('blog_home_meta_desc'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}" />
    <meta property="og:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="{{ (json_decode(nova_get_setting('blog_home_meta_title'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}">
    <meta name="twitter:description" content="{{ (json_decode(nova_get_setting('blog_home_meta_desc'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}">
    <meta name="twitter:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}">

    {!! nova_get_setting('home_schema') !!}
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
                        <span itemprop="name">{{__('blog.home')}}</span>
                        <meta itemprop="position" content="2" />
                    </li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>


<div class="mb-6 bg-md-transparent py-0">
    <div class="container">


        <div class="row mb-2" bis_skin_checked="1">
            @foreach ($articles as $article)
            <div class="col-md-6" bis_skin_checked="1">
                <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 mx-1 shadow-sm h-md-250 position-relative" bis_skin_checked="1">
                  <div class="col p-4 d-flex flex-column position-static" bis_skin_checked="1">
                    {{-- <strong class="d-inline-block mb-2 text-primary-emphasis">World</strong> --}}
                    <h3 class="mb-0">{{ $article->name }}</h3>
                    <div class="mb-1 text-body-secondary" bis_skin_checked="1">{{ date('d-m-Y', strtotime($article->created_at)) }}</div>
                    <p class="card-text mb-auto">{!! $article->short_desc !!}</p>
                    <a href="blog/{{ $article->slug }}" class="icon-link gap-1 icon-link-hover stretched-link">
                        {{__('blog.continue_reading')}}
                    </a>
                  </div>
                  <div class="col-auto d-none d-lg-block p-0" bis_skin_checked="1">
                    <img class="bd-placeholder-img" width="200" height="250" src="{{$article->getFirstMediaUrlOrDefault(ARTICLE_PATH, 'size_100_100')['url']}}">
                  </div>
                </div>
              </div>
            @endforeach


          </div>

    </div>
</div>

@endsection
