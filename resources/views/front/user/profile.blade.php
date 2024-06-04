@extends('front.layouts.master')
@section('metaTags')
    <title>YouMats | {{$user->name}}</title>
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
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{route('home')}}">{{__('general.home')}}</a></li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">{{$user->name}}</li>
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
                    <img loading="lazy" src="{{ $user->getFirstMediaUrlOrDefault(USER_COVER, 'size_1350_300')['url'] }}" class="photo_cover_vendor">
                </div>
                <img loading="lazy" src="{{ $user->getFirstMediaUrlOrDefault(USER_PROFILE, 'size_200_200')['url'] }}" class="photo_profile_vendor">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="info_main_vendor">
                    <h3>{{$user->name}}</h3>
                    <p>{{__('profile.join_at')}} <b>{{$user->created_at->format('d F Y')}}</b></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="position-relative position-md-static px-md-6">
                    <ul class="nav nav-classic nav-tab nav-tab-lg justify-content-xl-center flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble border-0 pb-1 pb-xl-0 mb-n1 mb-xl-0" id="pills-tab-8" role="tablist">
                        <li class="nav-item flex-shrink-0 flex-xl-shrink-1 z-index-2">
                            <a class="nav-link active" id="Jpills-one-example1-tab" data-toggle="pill" href="#Jpills-one-example1" role="tab" aria-controls="Jpills-one-example1" aria-selected="true">{{__('profile.info')}}</a>
                        </li>
                        <li class="nav-item flex-shrink-0 flex-xl-shrink-1 z-index-2">
                            <a class="nav-link" id="Jpills-four-example1-tab" data-toggle="pill" href="#Jpills-four-example1" role="tab" aria-controls="Jpills-four-example1" aria-selected="false"> {{ $user->type == 'company' ? __('profile.quotes') : __('profile.orders') }} </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-12">
                <div class="borders-radius-17 border p-4 mt-4 mt-md-0 px-lg-10 py-lg-9 mb-5">
                    <div class="tab-content" id="Jpills-tabContent">
                        <div class="tab-pane fade active show" id="Jpills-one-example1" role="tabpanel" aria-labelledby="Jpills-one-example1-tab">
                            <div class="block_info_vendor">
                                @if(Session::has('message'))
                                    <div class="alert alert-success">
                                        {{ Session::get('message') }}
                                    </div>
                                @endif
                                <form method="post" action="{{route('front.user.updateProfile')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="box_img_v">
                                                <img loading="lazy" src="{{ $user->getFirstMediaUrlOrDefault(USER_COVER, 'size_height_300')['url'] }}" class="photo_cover_vendor">
                                            </div>
                                        </div>
                                        <div class="col-md-3 ml-auto">
                                            <div class="box_img_profile">
                                                <img loading="lazy" src="{{ $user->getFirstMediaUrlOrDefault(USER_PROFILE, 'size_200_200')['url'] }}" class="photo_cover_vendor">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="js-form-message form-group mb-5">
                                                <label class="form-label">{{__('profile.cover')}}</label>
                                                <div class="box">
                                                    <input type="file" name="cover" id="cover" class="inputfile inputfile-6 @error('cover') is-invalid @enderror" />
                                                    <label for="cover">
                                                        <span></span>
                                                        <strong>{{__('profile.choose_file')}}</strong>
                                                    </label>
                                                    @error('cover')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="js-form-message form-group mb-5">
                                                <label class="form-label">{{__('profile.profile')}}</label>
                                                <div class="box">
                                                    <input type="file" name="profile" id="profile" class="inputfile inputfile-6 @error('profile') is-invalid @enderror" />
                                                    <label for="profile">
                                                        <span></span>
                                                        <strong>{{__('profile.choose_file')}}</strong>
                                                    </label>
                                                    @error('profile')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="js-form-message form-group mb-5">
                                                <label for="name" class="form-label">{{__('profile.name')}} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" required value="{{$user->name}}">
                                                @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="js-form-message form-group mb-5">
                                                <label for="email" class="form-label">{{__('profile.email')}} <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" disabled value="{{$user->email}}">
                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="js-form-message form-group mb-5">
                                                <label for="phone" class="form-label">{{__('profile.phone')}} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" required value="{{$user->phone}}">
                                                @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="js-form-message form-group mb-5">
                                                <label for="phone2" class="form-label">{{__('profile.phone2')}}</label>
                                                <input type="text" class="form-control @error('phone2') is-invalid @enderror" name="phone2" id="phone2" value="{{$user->phone2}}">
                                                @error('phone2')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="js-form-message form-group mb-5">
                                                <label for="address" class="form-label">{{__('profile.address')}} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" id="address" required value="{{$user->address}}">
                                                @error('address')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="js-form-message form-group mb-5">
                                                <label for="address2" class="form-label">{{__('profile.address2')}}</label>
                                                <input type="text" class="form-control @error('address2') is-invalid @enderror" name="address2" id="address2" value="{{$user->address2}}">
                                                @error('address2')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <hr>
                                            <div class="js-form-message form-group mb-5">
                                                <label class="form-label">{{ __('profile.location') }}</label>
                                                {!! generate_map() !!}
                                                <input type="hidden" class="lat" value="{{$user->latitude}}" readonly name="latitude" required>
                                                <input type="hidden" class="lng" value="{{$user->longitude}}" readonly name="longitude" required>
                                            </div>
                                        </div>
                                        @if($user->type == 'company')
                                        <div class="col-md-12">
                                            <div class="js-form-message form-group mb-5">
                                                <label class="form-label">{{__('profile.licenses')}}</label>
                                                <div class="box">
                                                    <input type="file" name="licenses[]" id="licenses" class="inputfile inputfile-6 @error('licenses') is-invalid @enderror" multiple />
                                                    <label for="licenses">
                                                        <span></span>
                                                        <strong>{{__('profile.choose_files')}}</strong>
                                                    </label>
                                                    @error('licenses')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                                <div class="box">
                                                    @foreach($user->getMedia(COMPANY_PATH) as $license)
                                                        <img loading="lazy" src="{{$license->getFullUrl()}}" class="img-thumbnail" width="100px" />
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-md-6">
                                            <div class="js-form-message form-group mb-5">
                                                <label for="password" class="form-label">{{__('profile.password')}}</label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password">
                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="js-form-message form-group mb-5">
                                                <label for="password_confirmation" class="form-label">{{__('profile.confirm_password')}}</label>
                                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation">
                                                @error('password_confirmation')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-6">
                                                <button type="submit" class="btn btn-primary-dark-w px-5 text-white mr-2"> <i class="fas fa-save"></i>{{__('profile.save_button')}}</button>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="Jpills-four-example1" role="tabpanel" aria-labelledby="Jpills-four-example1-tab">
                            <div class="container">
                                <div class="row">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ $user->type == 'company' ? __('profile_quote') : __('profile_order') }}#</th>
                                                <th scope="col">{{__('profile.date')}}</th>
                                                @if($user->type != 'company')
                                                    <th scope="col">{{__('profile.total_price')}}</th>
                                                    <th scope="col">{{__('profile.payment_status')}}</th>
                                                @endif
                                                <th scope="col">{{__('profile.status')}}</th>
                                                <th scope="col">{{__('profile.details')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $order)
                                            <tr>
                                                <th scope="row">{{ $user->type == 'company' ? $order->quote_no : $order->order_id }}</th>
                                                <td>{{$order->created_at->format('l, F d, Y h:i A')}}</td>
                                                @if($user->type != 'company')
                                                    <td>{{getCurrency('symbol')}} {{$order->total_price}}</td>
                                                    <td>{{$order->payment_status}}</td>
                                                @endif
                                                <td>{{$order->status}}</td>
                                                <td class="text-center">
                                                    <a href="#" data-toggle="modal" data-target="#order{{$order->id}}">{{__('profile.view')}} <i class="far fa-eye"></i></a>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="order{{$order->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">{{ $user->type == 'company' ? __('profile_quote') : __('profile_order') }}#<b> {{ $user->type == 'company' ? $order->quote_no : $order->order_id }}</b></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-12 col-xl-12">
                                                                    <div>
                                                                        <ul class="list-unstyled-branches list_order_vendor mb-6">
                                                                            <li class="row">
                                                                                <div class="col-md-4">
                                                                                    <b>{{__('profile.date')}}</b>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <span>{{$order->created_at->format('l, F d, Y h:i A')}}</span>
                                                                                </div>
                                                                            </li>
                                                                            <li class="row">
                                                                                <div class="col-md-4">
                                                                                    <b>{{__('profile.name')}}</b>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <span>{{$order->name}}</span>
                                                                                </div>
                                                                            </li>
                                                                            <li class="row">
                                                                                <div class="col-md-4">
                                                                                    <b>{{__('profile.email')}}</b>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <span>{{$order->email}}</span>
                                                                                </div>
                                                                            </li>
                                                                            <li class="row">
                                                                                <div class="col-md-4">
                                                                                    <b>{{__('profile.phone')}}</b>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <span>{{$order->phone}}</span>
                                                                                </div>
                                                                            </li>
                                                                            @if(isset($order->phone2))
                                                                                <li class="row">
                                                                                    <div class="col-md-4">
                                                                                        <b>{{__('profile.phone2')}}</b>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <span>{{$order->phone2}}</span>
                                                                                    </div>
                                                                                </li>
                                                                            @endif
                                                                            <li class="row">
                                                                                <div class="col-md-4">
                                                                                    <b>{{__('profile.address')}}</b>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <span>{{$order->address}}</span>
                                                                                </div>
                                                                            </li>
                                                                            @if(isset($order->building_number))
                                                                                <li class="row">
                                                                                    <div class="col-md-4">
                                                                                        <b>{{__('profile.building_number')}}</b>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <span>{{$order->building_number}}</span>
                                                                                    </div>
                                                                                </li>
                                                                            @endif
                                                                            @if(isset($order->street))
                                                                                <li class="row">
                                                                                    <div class="col-md-4">
                                                                                        <b>{{__('profile.street')}}</b>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <span>{{$order->street}}</span>
                                                                                    </div>
                                                                                </li>
                                                                            @endif
                                                                            @if(isset($order->district))
                                                                                <li class="row">
                                                                                    <div class="col-md-4">
                                                                                        <b>{{__('profile.district')}}</b>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <span>{{$order->district}}</span>
                                                                                    </div>
                                                                                </li>
                                                                            @endif
                                                                            @if(isset($order->city))
                                                                                <li class="row">
                                                                                    <div class="col-md-4">
                                                                                        <b>{{__('profile.city')}}</b>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <span>{{$order->city}}</span>
                                                                                    </div>
                                                                                </li>
                                                                            @endif
                                                                            @if($user->type != 'company')
                                                                            @if($order->coupon_code)
                                                                                <li class="row">
                                                                                    <div class="col-md-4">
                                                                                        <b>{{__('profile.coupon_code')}}</b>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <span>{{$order->coupon_code}}</span>
                                                                                    </div>
                                                                                </li>
                                                                            @endif
                                                                            <li class="row">
                                                                                <div class="col-md-4">
                                                                                    <b>{{__('profile.payment_method')}}</b>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <span>{{$order->payment_method}}</span>
                                                                                </div>
                                                                            </li>
                                                                            <li class="row">
                                                                                <div class="col-md-4">
                                                                                    <b>{{__('profile.payment_status')}}</b>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <span>{{$order->payment_status}}</span>
                                                                                </div>
                                                                            </li>
                                                                            @endif
                                                                            <li class="row">
                                                                                <div class="col-md-4">
                                                                                    <b>{{__('profile.status')}}</b>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <span>{{$order->status}}</span>
                                                                                </div>
                                                                            </li>
                                                                            @if($order->notes)
                                                                            <li class="row">
                                                                                <div class="col-md-4">
                                                                                    <b>{{__('profile.notes')}}</b>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <span>{{$order->notes}}</span>
                                                                                </div>
                                                                            </li>
                                                                            @endif
                                                                            @if($user->type != 'company')
                                                                                <li class="row">
                                                                                    <div class="col-md-4">
                                                                                        <b>{{__('profile.total_price')}}</b>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <span>{{getCurrency('symbol')}} {{$order->total_price}}</span>
                                                                                    </div>
                                                                                </li>
                                                                            @endif
                                                                            @if(count($order->items) > 0)
                                                                                <h4>{{ $user->type == 'company' ? __('profile_quotes_items') : __('profile_orders_items') }}</h4>
                                                                                @foreach($order->items as $item)
                                                                                    <li class="row">
                                                                                        <div class="col-md-4">
                                                                                            <b>{{__('profile.item_name')}}</b>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <span>{{ $item->product->name ?? __('profile.product') }}</span>
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="row">
                                                                                        <div class="col-md-4">
                                                                                            <b>{{__('profile.quantity')}}</b>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <span>{{ $item->quantity }}</span>
                                                                                        </div>
                                                                                    </li>
                                                                                @if($user->type != 'company')
                                                                                    <li class="row">
                                                                                        <div class="col-md-4">
                                                                                            <b>{{__('profile.price')}}</b>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <span>{{ $item->price }}</span>
                                                                                        </div>
                                                                                    </li>
                                                                                    <li class="row">
                                                                                        <div class="col-md-4">
                                                                                            <b>{{__('profile.status')}}</b>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <span>{{ $item->status }}</span>
                                                                                        </div>
                                                                                    </li>
                                                                                    @if($item->status == 'refused')
                                                                                        <li class="row">
                                                                                            <div class="col-md-4">
                                                                                                <b>{{__('profile.refused_note')}}:</b>
                                                                                            </div>
                                                                                            <div class="col-md-8">
                                                                                                <span>{{$item->refused_note}}</span>
                                                                                            </div>
                                                                                        </li>
                                                                                    @endif
                                                                                    <li class="row">
                                                                                        <div class="col-md-4">
                                                                                            <b>{{__('profile.payment_status')}}</b>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <span>{{ $item->payment_status }}</span>
                                                                                        </div>
                                                                                    </li>
                                                                                    @endif
                                                                                    <hr />
                                                                                    @endforeach
                                                                            @endif
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Modal -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extraScripts')
    <script defer type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('FRONT_MAPS_KEY')}}&libraries=places&sensor=false"></script>
    <script defer src="{{front_url()}}/assets/js/map.js"></script>
@endsection
