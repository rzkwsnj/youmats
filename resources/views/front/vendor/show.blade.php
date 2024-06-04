@extends('front.layouts.master')
@section('metaTags')
    <title>{{getMetaTag($vendor, 'meta_title', $vendor->name)}}</title>
    <meta name="description" content="{{getMetaTag($vendor, 'meta_desc', $vendor->name)}}">
    <meta name="keywords" content="{{getMetaTag($vendor, 'meta_keywords', '')}}">

    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="{{getMetaTag($vendor, 'meta_title', $vendor->name)}}" />
    <meta property="og:description" content="{{getMetaTag($vendor, 'meta_desc', $vendor->name)}}" />
    <meta property="og:image" content="{{$vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_height_150')['url']}}" />

    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="{{getMetaTag($vendor, 'meta_title', $vendor->name)}}">
    <meta name="twitter:description" content="{{getMetaTag($vendor, 'meta_desc', $vendor->name)}}">
    <meta name="twitter:image" content="{{$vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_height_150')['url']}}">

    <link rel="canonical" href="{{url()->current()}}" />

    {!! $vendor->schema !!}
@endsection
@section('content')
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <!-- breadcrumb -->
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble" itemscope itemtype="https://schema.org/BreadcrumbList">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"  itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a itemprop="item" href="{{route('home')}}"><span itemprop="name">{{__('general.home')}}</span></a>
                            <meta itemprop="position" content="1" />
                        </li>
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page"><span itemprop="name">{{ $vendor->name }}</span>
                            <meta itemprop="position" content="2" />
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- End breadcrumb -->
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="img_vendor">
                    <img loading="lazy" src="{{ $vendor->getFirstMediaUrlOrDefault(VENDOR_COVER, 'size_1350_300')['url'] }}" class="photo_cover_vendor">
                </div>
                <img loading="lazy" src="{{ $vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_200_200')['url'] }}" class="photo_profile_vendor">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="info_main_vendor">
                    <h1>{{ $vendor->name }}</h1>
                    @if(isset($vendor->type))
                    <label style="font-weight: bold">{{__("general.$vendor->type")}}</label>
                    @endif
                    <p>{{ __('general.started') . ' ' . $vendor->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="position-relative position-md-static px-md-6">
                    <ul class="nav nav-classic nav-tab nav-tab-lg justify-content-xl-center flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble border-0 pb-1 pb-xl-0 mb-n1 mb-xl-0" id="pills-tab-8" role="tablist">
                        <li class="nav-item flex-shrink-0 flex-xl-shrink-1 z-index-2">
                            <a class="nav-link" id="Jpills-one-example1-tab" data-toggle="pill" href="#Jpills-one-example1" role="tab" aria-controls="Jpills-one-example1" aria-selected="true">{{ __('vendor.information') }}</a>
                        </li>
                        <li class="nav-item flex-shrink-0 flex-xl-shrink-1 z-index-2">
                            <a class="nav-link active" id="Jpills-two-example1-tab" data-toggle="pill" href="#Jpills-two-example1" role="tab" aria-controls="Jpills-two-example1" aria-selected="false">{{ __('vendor.products') }}</a>
                        </li>
                        <li class="nav-item flex-shrink-0 flex-xl-shrink-1 z-index-2">
                            <a class="nav-link" id="Jpills-three-example1-tab" data-toggle="pill" href="#Jpills-three-example1" role="tab" aria-controls="Jpills-three-example1" aria-selected="false">{{ __('vendor.branches') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-12">
                <div class="borders-radius-17 border p-4 mt-4 mt-md-0 px-lg-10 py-lg-9 mb-5">
                    <div class="tab-content" id="Jpills-tabContent">
                    <div class="tab-pane fade" id="Jpills-one-example1" role="tabpanel" aria-labelledby="Jpills-one-example1-tab">
                        <div class="block_info_vendor">
                            <div class="row">
                                <div class="col-md-12 mb-5">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th class="pt-3 pb-3 pl-3 w-25">{{ __('vendor.email') }}</th>
                                            <td class="pt-3 pb-3 pl-3">{{ $vendor->email }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pt-3 pb-3 pl-3 w-25">{{ __('vendor.address') }}</th>
                                            <td class="pt-3 pb-3 pl-3">{{ $vendor->address }}</td>
                                        </tr>
                                        @if(count($vendor->contacts))
                                            @foreach($vendor->contacts as $contact)
                                                <tr>
                                                    <th class="pt-3 pb-3 pl-3 w-25">{{ __('vendor.contacts') . ' ' . $loop->iteration }}</th>
                                                    <td>
                                                        <label style="font-weight: bold">{{__('vendor.person_name')}}: </label>
                                                        <span>{{ $contact['person_name'] }}</span><br/>
                                                        <label style="font-weight: bold">{{__('vendor.phone')}}: </label>
                                                        <span>{{ $contact['phone'] }}</span><br/>
                                                        <label style="font-weight: bold">{{__('vendor.email')}}: </label>
                                                        <span>{{ $contact['email'] }}</span><br/>
                                                        @if(isset($contact['cities']))
                                                        <label style="font-weight: bold">{{__('vendor.cities')}}: </label>
                                                        <span>
                                                            @php
                                                                $cities = \App\Models\City::whereIn('id', $contact['cities'])->pluck('name', 'id');
                                                            @endphp
                                                            @foreach($contact['cities'] as $city)
                                                                {{$cities[$city]}}
                                                                @if(!$loop->last)
                                                                    -
                                                                @endif
                                                            @endforeach
                                                        </span><br/>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if($vendor->facebook_url)
                                            <tr>
                                                <th class="pt-3 pb-3 pl-3 w-25">{{ __('vendor.facebook_url')  }}</th>
                                                <td class="pt-3 pb-3 pl-3">{{ $vendor->facebook_url }}</td>
                                            </tr>
                                        @endif
                                        @if($vendor->twitter_url)
                                            <tr>
                                                <th class="pt-3 pb-3 pl-3 w-25">{{ __('vendor.twitter_url')  }}</th>
                                                <td class="pt-3 pb-3 pl-3">{{ $vendor->twitter_url }}</td>
                                            </tr>
                                        @endif
                                        @if($vendor->youtube_url)
                                            <tr>
                                                <th class="pt-3 pb-3 pl-3 w-25">{{ __('vendor.youtube_url')  }}</th>
                                                <td class="pt-3 pb-3 pl-3">{{ $vendor->youtube_url }}</td>
                                            </tr>
                                        @endif
                                        @if($vendor->instagram_url)
                                            <tr>
                                                <th class="pt-3 pb-3 pl-3 w-25">{{ __('vendor.instagram_url')  }}</th>
                                                <td class="pt-3 pb-3 pl-3">{{ $vendor->instagram_url }}</td>
                                            </tr>
                                        @endif
                                        @if($vendor->pinterest_url)
                                            <tr>
                                                <th class="pt-3 pb-3 pl-3 w-25">{{ __('vendor.pinterest_url')  }}</th>
                                                <td class="pt-3 pb-3 pl-3">{{ $vendor->pinterest_url }}</td>
                                            </tr>
                                        @endif
                                        @if($vendor->website_url)
                                            <tr>
                                                <th class="pt-3 pb-3 pl-3 w-25">{{ __('vendor.website_url')  }}</th>
                                                <td class="pt-3 pb-3 pl-3">{{ $vendor->website_url }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th class="pt-3 pb-3 pl-3 w-25">{{ __('vendor.status') }}</th>
                                            <td class="pt-3 pb-3 pl-3">@if($vendor->active) <i class="fas fa-check-circle"></i> @else <i class="fas fa-times-circle"></i> @endif</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade active show" id="Jpills-two-example1" role="tabpanel" aria-labelledby="Jpills-one-example1-tab">
                        @if(count($products) > 0)
                            <ul class="row list-unstyled products-group no-gutters">
                                @foreach($products as $product)
                                    <li class="col-6 col-md-3 col-wd-2gdot4 product-item">
                                        @include('front.layouts.partials.product_box', ['product' => $product, 'view' => 'grid'])
                                    </li>
                                @endforeach
                            </ul>
                            {{ $products->onEachSide(0)->links() }}
                        @else
                            <h4>{{ __('general.no_products') }}</h4>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="Jpills-three-example1" role="tabpanel" aria-labelledby="Jpills-one-example1-tab">
                        @if(count($vendor->branches) > 0)
                            @foreach($vendor->branches as $branch)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="border-bottom border-color-1 mb-5">
                                            <h3 class="section-title mb-0 pb-2 font-size-25"> {{ $branch->name }} ( {{ $branch->city->name }} ) </h3>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-xl-8">
                                        <div class="map_branches">
                                            <iframe src="https://maps.google.com/maps?q={{ $branch->latitude }},{{ $branch->longitude }}&hl=es&z=14&amp;output=embed" width="100%" height="250" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xl-4">
                                        <h5 class="font-size-14 font-weight-bold mb-3">{{ __('general.main_info') }}</h5>
                                        <div class="">
                                            <ul class="list-unstyled-branches mb-6">
                                                <li class="row">
                                                    <div class="col-md-2">
                                                        <i class="fas fa-phone"></i>
                                                    </div>
                                                    <div class="col-md-10 mt-2">
                                                        <b>{{ __('vendor.phone') }}:</b>
                                                        <span class=""> {{ $branch->phone_number }} </span>
                                                    </div>
                                                </li>
                                                @if($branch->fax)
                                                    <li class="row">
                                                        <div class="col-md-2">
                                                            <i class="fas fa-fax"></i>
                                                        </div>
                                                        <div class="col-md-10 mt-2">
                                                            <b>{{ __('vendor.fax') }}:</b>
                                                            <span class=""> {{ $branch->fax }} </span>
                                                        </div>
                                                    </li>
                                                @endif
                                                @if($branch->website)
                                                    <li class="row">
                                                        <div class="col-md-2">
                                                            <i class="fas fa-globe-americas"></i>
                                                        </div>
                                                        <div class="col-md-10 mt-2">
                                                            <b>{{ __('vendor.website_url') }}:</b>
                                                            <span class=""> {{ $branch->website }} </span>
                                                        </div>
                                                    </li>
                                                @endif
                                                <li class="row">
                                                    <div class="col-md-2">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                    </div>
                                                    <div class="col-md-10 mt-2">
                                                        <b>{{ __('vendor.address') }}:</b>
                                                        <span class=""> {{ $branch->address }} </span>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <h4>{{ __('vendor.no_branches') }}</h4>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
