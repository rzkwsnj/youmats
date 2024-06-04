@extends('front.layouts.master')
@section('metaTags')
    <title>YouMats | FAQs</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="" />
    <meta property="og:description" content="" />
    <meta property="og:image" content="" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="">
    <meta name="twitter:description" content="">
    <meta name="twitter:image" content="">
@endsection
@section('content')
    <div class="bg-gray-13 bg-md-transparent">
    <div class="container">
        <!-- breadcrumb -->
        <div class="my-md-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{route('home')}}">{{ __('general.home') }}</a></li>
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">{{ __('general.faqs') }}</li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-12 text-center">
                    <h1>{{ __('general.youmats_faq') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div id="basicsAccordion" class="mb-12">
                    @foreach($FAQs as $question)
                    <div class="card border-top-0 border-left-0 border-right-0 border-color-1 rounded-0">
                        <div class="card-header card-collapse bg-transparent-on-hover border-0" id="qu{{$question->id}}">
                            <h5 class="mb-0">
                                <button type="button" class="px-0 btn btn-link btn-block d-flex justify-content-between card-btn py-3 font-size-25 border-0 @if(!$loop->first) collapsed @endif" data-toggle="collapse" data-target="#q{{$question->id}}" @if($loop->first) aria-expanded="true" @else aria-expanded="false" @endif aria-controls="q{{$question->id}}">
                                    {{$question->question}}
                                    <span class="card-btn-arrow">
                                        <i class="fas fa-chevron-down text-gray-90 font-size-18"></i>
                                    </span>
                                </button>
                            </h5>
                        </div>
                        <div id="q{{$question->id}}" class="collapse @if($loop->first) show @endif" aria-labelledby="qu{{$question->id}}" data-parent="#basicsAccordion" style="">
                            <div class="card-body pl-0 pb-8">
                                <p class="mb-0">{!! $question->answer !!}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
