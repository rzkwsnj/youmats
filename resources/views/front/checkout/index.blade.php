@extends('front.layouts.master')
@section('metaTags')
    <title>YouMats | Checkout</title>
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
    <div class="container">
        <div class="mb-5">
            <h1 class="text-center">{{ is_company() ? __('checkout.get_quote_price') : __('checkout.checkout_title') }}</h1>
        </div>
        @if(!Auth::guard('web')->check())
            <!-- Accordion -->
            <div id="shopCartAccordion" class="accordion rounded mb-5">
                <!-- Card -->
                <div class="card border-0">
                    <div id="shopCartHeadingOne" class="alert alert-primary mb-0 text-white text-left" role="alert">
                        {{ __('checkout.returning_customer') }} <a href="#" class="alert-link collapsed text-white" data-toggle="collapse" data-target="#shopCartOne" aria-expanded="false" aria-controls="shopCartOne">{{__('checkout.login')}}</a>
                    </div>
                    <div id="shopCartOne" class="border border-top-0 collapse {{ ($errors->email || $errors->password) ? 'show' : '' }}" aria-labelledby="shopCartHeadingOne" data-parent="#shopCartAccordion" style="">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Form -->
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
{{--                                        <div class="row">--}}
{{--                                            <div class="mb-4">--}}
{{--                                                <h1 class="text-center col-md-12">{{ __('auth.login') }}</h1>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
                                        <div class="row pt-3">
                                            <div class="col-md-12">
                                                <div class="js-form-message form-group mb-5">
                                                    <label class="form-label" for="email">{{ __('auth.email') }}</label>
                                                    <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                                    @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="js-form-message form-group mb-5">
                                                    <label class="form-label" for="password">{{ __('auth.password_input') }}</label>
                                                    <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                                    @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="remember">
                                                            {{ __('auth.remember_me') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <button type="submit" class="btn btn-primary-dark-w px-5 text-white">{{ __('auth.login') }}</button>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                @if (Route::has('password.request'))
                                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                                        {{ __('auth.forget_password') }}
                                                    </a>
                                                @endif
                                                <a class="btn btn-link" href="{{route('register')}}">
                                                    {{ __('auth.register') }}
                                                </a>
                                            </div>

                                        </div>
                                    </form>
                                    <!-- End Form -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Card -->
            </div>
            <!-- End Accordion -->
        @endif

        <!-- Accordion -->
        <div id="shopCartAccordion1" class="accordion rounded mb-6">
            <!-- Card -->
            <div class="card border-0">
                <div id="shopCartHeadingTwo" class="alert alert-primary mb-0 text-white text-left" role="alert">
                    {{__('checkout.have_coupon')}} <a href="#" class="alert-link text-white" data-toggle="collapse" data-target="#shopCartTwo" aria-expanded="false" aria-controls="shopCartTwo">{{__('checkout.enter_code')}}</a>
                </div>
                <div id="shopCartTwo" class="collapse border border-top-0" aria-labelledby="shopCartHeadingTwo" data-parent="#shopCartAccordion1" style="">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12" style="padding: 15px">
                                <!-- Apply coupon Form -->
                                <form class="js-focus-state" action="{{ route('apply.coupon') }}" method="POST">
                                    @csrf
                                    <label class="sr-only">{{__('checkout.enter_code')}}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="{{__('checkout.enter_code')}}" @if($coupon) disabled @endif name="code" @if($coupon) value="{{ $coupon->name }}" @else value="{{ old('code') }}" @endif id="couponCode" required>
                                        <div class="input-group-append">
                                            <input type="submit" class="btn btn-block btn-dark px-4" @if($coupon) disabled @endif value="{{__('checkout.coupon_button')}}" />
                                        </div>
                                    </div>
                                </form>
                                <!-- End Apply coupon Form -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Card -->
        </div>
        <!-- End Accordion -->

        <form class="js-validate" novalidate="novalidate" action="{{ route('checkout') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row rtl">
                <div class="col-lg-7">
                    <div class="pb-7 mb-7">
                        <!-- Title -->
                        <div class="border-bottom border-color-1 mb-5">
                            <h3 class="section-title mb-0 pb-2 font-size-25">{{__('checkout.billing_details')}}</h3>
                        </div>
                        <!-- End Title -->
                        <!-- Billing Form -->
                        <div class="js-form-message form-group mb-5 rtl">
                            @if(!auth()->guard('web')->check() && !session()->has('userType'))
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="select_reg @if(!is_company()) active @endif" for="registerAsIndividual">
                                            <input type="radio" name="type" value="individual" id="registerAsIndividual">
                                            {{ __('auth.register_as_individual') }}
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="select_reg @if(is_company()) active @endif" for="registerAsCompany">
                                            <input type="radio" name="type" value="company" id="registerAsCompany">
                                            {{ __('auth.register_as_company') }}
                                        </label>
                                    </div>
                                </div>
                                @error('type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            @endif
                        </div>
                        <div class="row rtl">
                            <div class="col-md-6">
                                <!-- Input -->
                                <div class="js-form-message mb-6">
                                    <label class="form-label">
                                        {{__('checkout.name')}}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" value="{{ Auth::guard('web')->user()->name ?? old('name') }}" name="name" required="" data-msg="Please enter your frist name." data-error-class="u-has-error" data-success-class="u-has-success" autocomplete="off">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <!-- End Input -->
                            </div>
                            <div class="col-md-6">
                                <!-- Input -->
                                <div class="js-form-message mb-6">
                                    <label for="phone" class="form-label">
                                        {{__('checkout.phone')}}
                                    </label>
                                    <input type="tel" id="phone" class="form-control phoneNumber @error('phone') is-invalid @enderror"
                                           value="{{ old('phone') }}" required data-error-class="u-has-error" data-success-class="u-has-success">
                                    <input type="hidden" class="fullPhoneNumber" name="phone">
                                    @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <!-- End Input -->
                            </div>
                            <div class="col-md-6">
                                <!-- Input -->
                                <div class="js-form-message mb-6">
                                    <label class="form-label">
                                        {{__('checkout.email')}}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" value="{{ Auth::guard('web')->user()->email ?? old('email') }}" name="email" aria-label="jackwayley@gmail.com" required="" data-msg="Please enter a valid email address." data-error-class="u-has-error" data-success-class="u-has-success">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <!-- End Input -->
                            </div>
                            <div class="col-md-6">
                                <!-- Input -->
                                <div class="js-form-message mb-6">
                                    <label class="form-label">
                                        {{__('checkout.city')}}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" value="{{\App\Models\City::find(Session::get('city'))->name ?? Session::get('city')}}" disabled readonly>
{{--                                        <div class="dropdown bootstrap-select form-control js-select dropdown-select">--}}
{{--                                            <select class="form-control js-select selectpicker dropdown-select" readonly name="city" required data-msg="{{__('checkout.select_city')}}" data-error-class="u-has-error" data-success-class="u-has-success" data-live-search="true" data-style="form-control border-color-1 font-weight-normal" tabindex="-98">--}}
{{--                                                <option value="" disabled>{{__('checkout.select_city')}}</option>--}}
{{--                                                @foreach($cities as $city)--}}
{{--                                                    <option value="{{ $city->id }}" @if($city->id == Session::get('city')) selected @endif>{{ $city->name }}</option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}
{{--                                            @error('city')--}}
{{--                                                <span class="invalid-feedback" role="alert">--}}
{{--                                                    <strong>{{ $message }}</strong>--}}
{{--                                                </span>--}}
{{--                                            @enderror--}}
{{--                                        </div>--}}
                                </div>
                                <!-- End Input -->
                            </div>
                            <div class="col-md-6">
                                <!-- Input -->
                                <div class="js-form-message mb-6">
                                    <label class="form-label">
                                        {{ is_company() ? __('checkout.delivery_address') : __('checkout.address') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" value="{{ Auth::guard('web')->user()->address ?? old('address') }}" name="address" aria-label="470 Lucy Forks" required="" data-msg="Please enter a valid address." data-error-class="u-has-error" data-success-class="u-has-success">
                                    @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <!-- End Input -->
                            </div>
                            @if(!is_company())
                                <div class="col-md-6">
                                    <!-- Input -->
                                    <div class="js-form-message mb-6">
                                        <label class="form-label">
                                            {{__('checkout.building_number')}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" value="{{ old('building_number') }}" name="building_number" aria-label="470 Lucy Forks" required="" data-msg="Please enter a valid address." data-error-class="u-has-error" data-success-class="u-has-success">
                                        @error('building_number')
                                        <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <!-- End Input -->
                                </div>
                                <div class="col-md-6">
                                    <!-- Input -->
                                    <div class="js-form-message mb-6">
                                        <label class="form-label">
                                            {{__('checkout.street')}}
                                        </label>
                                        <input type="text" class="form-control" value="{{ old('street') }}" name="street" aria-label="470 Lucy Forks" data-msg="Please enter a valid address." data-error-class="u-has-error" data-success-class="u-has-success">
                                        @error('street')
                                        <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <!-- End Input -->
                                </div>
                                <div class="col-md-6">
                                    <!-- Input -->
                                    <div class="js-form-message mb-6">
                                        <label class="form-label">
                                            {{__('checkout.district')}}
                                        </label>
                                        <input type="text" class="form-control" value="{{ old('district') }}" name="district" data-msg="Please enter a valid address." data-error-class="u-has-error" data-success-class="u-has-success">
                                        @error('district')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <!-- End Input -->
                                </div>
                            @endif
                            @if(is_company())
                                <div class="col-md-6">
                                    <!-- Input -->
                                    <div class="js-form-message mb-6">
                                        <label class="form-label">
                                            {{__('checkout.delivery_time')}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select class="form-control" name="delivery_time" required data-msg="Please enter a delivery time." data-error-class="u-has-error" data-success-class="u-has-success">
                                                    @for($i = 1; $i<=10;$i++)
                                                        <option value="{{$i}}" @if($i == old('delivery_time')) selected @endif>{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-control" name="delivery_time_unit">
                                                    <option value="day" @if(old('delivery_time_unit') == 'day') selected @endif>{{__('checkout.day')}}</option>
                                                    <option value="week" @if(old('delivery_time_unit') == 'week') selected @endif>{{__('checkout.week')}}</option>
                                                    <option value="month" @if(old('delivery_time_unit') == 'month') selected @endif>{{__('checkout.month')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        @error('delivery_time')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                        @error('delivery_time_unit')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <!-- End Input -->
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">
                                        {{ __('checkout.attachments')}}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="attachments[]" class="form-control" multiple />

                                    @error('attachments')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <hr>
                                    <div class="js-form-message form-group mb-5">
                                        <label class="form-label">{{ __('checkout.location') }}</label>
                                        {!! generate_map() !!}
                                        <input type="hidden" class="lat" value="{{old('latitude')}}" readonly name="latitude" required>
                                        <input type="hidden" class="lng" value="{{old('longitude')}}" readonly name="longitude" required>
                                    </div>
                                </div>
                            @endif
                            <div class="w-100"></div>
                        </div>
                        <!-- End Billing Form -->

                        @if(!Auth::guard('web')->check())
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="js-form-message form-group py-5">
                                        <label class="form-label" for="signinSrPasswordExample1">
                                            {{ __('checkout.password')}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" class="form-control" name="password" id="signinSrPasswordExample1" placeholder="********" aria-label="********" required="" data-msg="Enter password." data-error-class="u-has-error" data-success-class="u-has-success">
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="js-form-message form-group py-5">
                                        <label class="form-label" for="signinSrPasswordExample1">
                                            {{ __('checkout.confirm_password')}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" class="form-control" name="password_confirmation" id="signinSrPasswordExample2" placeholder="********" aria-label="********" required="" data-msg="Enter password." data-error-class="u-has-error" data-success-class="u-has-success">
                                        @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                    @endif
                    <!-- Input -->
                        <div class="js-form-message mb-6 text-left rtl">
                            <label class="form-label">
                                {{ is_company() ? __('checkout.quote_notes') : __('checkout.order_notes') }}
                            </label>

                            <div class="input-group">
                                <textarea class="form-control p-5" rows="4" name="notes" placeholder="{{__('checkout.notes_placeholder')}}">{{old('notes')}}</textarea>
                            </div>
                        </div>
                        <!-- End Input -->
                    </div>
                </div>
                <div class="col-lg-5 mb-7 mb-lg-0">
                    <div class="pl-lg-3 ">
                        <div class="bg-gray-1 rounded-lg">
                            <!-- Order Summary -->
                            <div class="p-4 mb-4 checkout-table">
                                <!-- Title -->
                                <div class="border-bottom border-color-1 mb-5">
                                    <h3 class="section-title mb-0 pb-2 font-size-25">{{ is_company() ? __('checkout.your_quote') : __('checkout.your_order') }}</h3>
                                </div>
                                <!-- End Title -->

                                <!-- Product Content -->
                                <table class="checkout-table table rtl">
                                    <thead>
                                    <tr>
                                        <th class="product-name">{{__('checkout.product')}}</th>
                                        @if(!is_company())
                                            <th class="product-total checkout-left">{{ __('checkout.total') }}</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cartItems as $item)
                                            <tr class="cart_item">
                                                <td>{{ $item->name }}
                                                    <b>({{ $item->qty }}) @if(!is_company()) x ({{__('general.sar') . ' ' . number_format($item->price, 2)}}) @endif</b>
                                                </td>
                                                @if(!is_company())
                                                    <td class="checkout-left">{{ __('general.sar') . ' ' . number_format($item->qty * $item->price, 2) }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    @if(!is_company())
                                        <tfoot>
                                        <tr>
                                            <th>{{ __('checkout.subtotal') }}</th>
                                            <td class="checkout-left" style="width: 100px">{{ __('general.sar') . ' ' . Cart::subtotal() }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('checkout.shipping') }}</th>
                                            <td class="checkout-left" style="width: 100px">{{ __('general.sar') . ' ' . cart_delivery() }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('checkout.total') }}</th>
                                            <td class="checkout-left" style="width: 100px"><strong>{{ __('general.sar') . ' ' . cart_total() }}</strong></td>
                                        </tr>
                                        </tfoot>
                                    @endif
                                </table>
                                @if(!is_company())
                                    <!-- End Product Content -->
                                    <div class="border-top border-width-3 border-color-1 pt-3 mb-3">
                                        <!-- Basics Accordion -->
                                        <div id="basicsAccordion1">
                                            @foreach($paymentGateways as $gateway)
                                                <div class="border-bottom border-color-1 border-dotted-bottom">
                                                    <div class="p-3" id="basicsHeadingOne">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="gateway-{{$gateway->id}}" class="custom-control-input" name="payment_method" value="{{ $gateway->value }}" checked>
                                                            <label class="custom-control-label form-label" for="gateway-{{$gateway->id}}">
                                                                {{ $gateway->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @error('payment_method')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <!-- End Basics Accordion -->
                                    </div>
                                @endif
                                <div class="form-group text-left">
                                    <div class="form-check">
                                        <input class="form-check-input" @if(old('terms')) checked @endif type="checkbox" name="terms" value="true" id="defaultCheck10" data-msg="Please agree terms and conditions." data-error-class="u-has-error" data-success-class="u-has-success">
                                        <label class="form-check-label form-label rtl" for="defaultCheck10">
                                            <a href="{{url('/page/terms-and-conditions')}}" target="_blank" class="text-blue">{{__('checkout.terms_conditions')}}</a>
                                            <span class="text-danger">*</span>
                                        </label>
                                        @error('terms')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary-dark-w btn-block btn-pill font-size-20 mb-3 py-3">
                                    @if(is_company())
                                        {{__('checkout.get_quote')}}
                                    @else
                                        {{__('checkout.place_order')}}
                                    @endif
                                </button>
                                @if(!is_company())
                                    <div class="text-md rtl">
                                        <span class="d-inline-block p-1">
                                            <img loading="lazy" class="max-width-6" src="{{asset('assets/img')}}/mada.png">
                                        </span>
                                        <span class="d-inline-block p-1">
                                            <img loading="lazy" class="max-width-5" src="{{asset('assets/img')}}/patment-icon_1.png">
                                        </span>
                                        <span class="d-inline-block p-1">
                                            <img loading="lazy" class="max-width-5" src="{{asset('assets/img')}}/patment-icon_2.png">
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <!-- End Order Summary -->
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('extraScripts')
    <script defer type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('FRONT_MAPS_KEY')}}&libraries=places&sensor=false"></script>
    <script defer src="{{front_url()}}/assets/js/map.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(function(){
                @if(!is_company())
                $('#registerAsIndividual').attr('checked', 'checked');
                @elseif(is_company())
                $('#registerAsCompany').attr('checked', 'checked');
                @endif
            });
            $('.select_reg').on('click', function () {
                $('.select_reg').removeClass('active');
                $(this).addClass('active');
            });
        });
    </script>
@endsection
