@extends('front.layouts.master')
@section('metaTags')
    <title>{{ ($article->name) ?? env('APP_NAME') }}</title>
    <meta name="keywords" content="{{ ($article->meta_keywords) ?? env('APP_NAME') }}">
    <meta name="description" content="{{ ($article->meta_desc) ?? env('APP_NAME') }}">
    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="{{ ($article->meta_title) ?? env('APP_NAME') }}" />
    <meta property="og:description" content="{{ ($article->meta_desc) ?? env('APP_NAME') }}" />
    <meta property="og:image" content="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="{{ ($article->meta_title) ?? env('APP_NAME') }}">
    <meta name="twitter:description" content="{{ ($article->meta_desc) ?? env('APP_NAME') }}">
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
                        <a itemprop="item" href="{{route('front.blog.index')}}"><span itemprop="name">{{__('blog.home')}}</span></a>
                        <meta itemprop="position" content="2" />
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">
                        <span itemprop="name">{{ $article->name }}</span>
                        <meta itemprop="position" content="3" />
                    </li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>


<div class="mb-6 bg-md-transparent py-0">
    <div class="container">
        <div class="row g-5" bis_skin_checked="1">
            <div class="col-md-8" bis_skin_checked="1">

              <article class="blog-post">
                <h1 class="display-5 link-body-emphasis mb-1">{{ $article->name }}</h1>
                <p class="blog-post-meta border-bottom">{{ date('d-m-Y', strtotime($article->created_at)) }}</p>
                <img class="bd-placeholder-img " width="100%" src="{{$article->getFirstMediaUrlOrDefault(ARTICLE_PATH, 'size_100_100')['url']}}">
                <hr>
                {!! $article->desc !!}
              </article>



            </div>

            <div class="col-md-4" bis_skin_checked="1">
              <div class="position-sticky" style="top: 2rem;" bis_skin_checked="1">
                <div class="p-4 pt-8 mb-3 bg-body-tertiary rounded" bis_skin_checked="1">
                  <h4 class="fst-italic">{{__('blog.about')}}</h4>
                  <p class="mb-0">
                    {!! $article->short_desc !!}
                  </p>
                </div>

                <div bis_skin_checked="1">
                  <h4 class="fst-italic">{{__('blog.recent_posts')}}</h4>
                  <ul class="list-unstyled">
                    @foreach ($articles as $single_article)
                        <li>
                            <a class="d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center py-3 link-body-emphasis text-decoration-none border-top" href="#">
                                <img class="bd-placeholder-img" width="100%" height="96" src="{{$single_article->getFirstMediaUrlOrDefault(ARTICLE_PATH, 'size_100_100')['url']}}">
                                <div class="col-lg-8" bis_skin_checked="1">
                                    <h6 class="mb-0">{{  $single_article->name }}</h6>
                                    <small class="text-body-secondary">{{ date('d-m-Y', strtotime($single_article->created_at)) }}</small>
                                </div>
                            </a>
                        </li>
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>
          </div>


{{--
        <div class="row mb-2" bis_skin_checked="1">
                  <div class="col p-4 d-flex flex-column position-static" bis_skin_checked="1">
                    <h1 class="mb-0"></h1>
                    <div class="mb-1 text-body-secondary" bis_skin_checked="1">Nov 12</div>
                    <p class="card-text mb-auto">{!! $article->short_desc !!}</p>
                    <a href="blog/{{ $article->slug }}" class="icon-link gap-1 icon-link-hover stretched-link">
                        {{__('blog.continue_reading')}}
                    </a>
                  </div>
                  <div class="col-auto d-none d-lg-block p-0" bis_skin_checked="1">
                  </div>

        </div>

--}}
    </div>
</div>


@endsection
