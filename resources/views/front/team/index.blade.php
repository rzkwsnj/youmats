@extends('front.layouts.master')
@section('metaTags')
    <title>{{__('general.team_title')}}</title>
    <meta name="description" content="{{__('general.team_title')}}">
    <meta name="keywords" content="">
    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="{{__('general.team_title')}}" />
    <meta property="og:description" content="{{__('general.team_title')}}" />
    <meta property="og:image" content="" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="{{__('general.team_title')}}">
    <meta name="twitter:description" content="{{__('general.team_title')}}">
    <meta name="twitter:image" content="">
@endsection
@section('content')
<div class="bg-gray-13 bg-md-transparent">
    <div class="container">
        <!-- breadcrumb -->
        <div class="my-md-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{route('home')}}">{{__('general.home')}}</a></li>
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">{{__('general.team_title')}}</li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>
<section class="pb-5">
    <div class="container">
        <div class="d-flex justify-content-between flex-lg-nowrap flex-wrap border-md-down-top-0 border-md-down-bottom-0 mb-3">
            <h3 class="section-title section-title__full mb-0 pb-2 font-size-22">{{__('general.team_main_title')}}</h3>
        </div>
        <div class="row">
            @foreach($team as $member)
            <div class="col-xs-12 col-sm-6 col-md-3">
                <div class="image-flip" >
                    <div class="mainflip flip-0">
                        <div class="frontside">
                            <div class="card">
                                <div class="card-body text-center">
                                    <p><img loading="lazy" class="img-fluid" src="{{$member->getFirstMediaUrlOrDefault(TEAM_PATH, 'size_120_120')['url']}}" alt="{{$member->getFirstMediaUrlOrDefault(TEAM_PATH)['alt']}}" title="{{$member->getFirstMediaUrlOrDefault(TEAM_PATH)['title']}}"></p>
                                    <h4 class="card-title">{{$member->name}}</h4>
                                    <p class="card-text">{{$member->position}}</p>
                                    <a href="#" class="btn btn-primary btn-sm icon-plus_team"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="backside">
                            <div class="card">
                                <div class="card-body text-center mt-4">
                                    <h4 class="card-title">{{$member->name}}</h4>
                                    <p class="card-text">{!! \Str::limit($member->info, 200) !!}</p>
                                    <ul class="list-inline">
                                        @if(isset($member->facebook))
                                        <li class="list-inline-item">
                                            <a class="social-icon text-xs-center" target="_blank" href="{{$member->facebook}}">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                        </li>
                                        @endif
                                        @if(isset($member->twitter))
                                        <li class="list-inline-item">
                                            <a class="social-icon text-xs-center" target="_blank" href="{{$member->twitter}}">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                        </li>
                                        @endif
                                        @if(isset($member->gmail))
                                        <li class="list-inline-item">
                                            <a class="social-icon text-xs-center" target="_blank" href="mailto:{{$member->gmail}}">
                                                <i class="fab fa-google"></i>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
