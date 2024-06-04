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
    <style>
        .page_404 {
            padding:20px 0 40px;
        }

        .four_zero_four_bg {
            background-image: url(https://cdn.dribbble.com/users/527451/screenshots/3115734/media/670f1ca9b879ca3da1f6e3f6c7bb0f3a.gif);
            width: 800px;
            height: 600px;
            background-position: center;
            margin: auto;
        }

        .page_404 h1 {
            font-size:50px;
        }

        .content_box_404 {
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .page_404 h1 {
                font-size: 30px;
            }

            .four_zero_four_bg {
                width: 100%;
                height: 500px;
            }
        }
    </style>

    <section class="page_404">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>404 | {{ __('general.404_not_found') }}</h1>
                    <div class="four_zero_four_bg"></div>
                    <div class="content_box_404">
                        <a href="{{ route('home') }}" class="btn btn-primary-dark-w px-5">{{ __('general.go_to_home') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
