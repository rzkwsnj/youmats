@extends('front.layouts.master')
@section('metaTags')
    <title>{{env('APP_NAME')}} | {{__('auth.register')}}</title>
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
<div id="main">
    <section class="section-login">
        <div class="section-main" style="width: 90% !important;">
            <div class="register-r section-login-2">
                <div class="section-login-2-main">
                    <h1 class="section-login-2-title">{{ __('auth.vendor_register') }}</h1>
                <div class="row">
                    <div class="col-md-12">
                        <div class="tab-content" id="Jpills-tabContent">
                            <div class="tab-pane fade active show" id="Jpills-one-example1" role="tabpanel" aria-labelledby="Jpills-one-example1-tab">
                                @error('files')
                                <div class="alert alert-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                                <form method="POST" class="section-login-2-form mt-1" action="{{ route('vendor.register') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 login-form-1">
                                                <label for="name_en" class="form-label">{{ __('auth.name') }} <span class="text-danger">({{ __('auth.in_english') }})*</span></label>
                                                <input type="text" class="form-control @error('name_en') is-invalid @enderror" id="name_en" name="name_en" value="{{ old('name_en') }}" required autocomplete="name_en" autofocus>
                                                @error('name_en')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                        </div>
                                        <div class="col-md-6 login-form-1">
                                                <label for="name_ar" class="form-label">{{ __('auth.name') }} <span class="text-danger">({{ __('auth.in_arabic') }})*</span></label>
                                                <input type="text" class="form-control rtl_important @error('name_ar') is-invalid @enderror" id="name_ar" name="name_ar" value="{{ old('name_ar') }}" required autocomplete="name_ar" autofocus>
                                                @error('name_ar')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                        </div>
                                        <div class="col-md-6 login-form-2">
                                                <label for="email" class="form-label">{{ __('auth.email') }} <span class="text-danger">*</span></label>
                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                        </div>
                                        <div class="col-md-6 login-form-2">
                                                <label for="phone" class="form-label">{{ __('auth.phone') }} <span class="text-danger">*</span></label>
                                                <input type="tel" id="phone" class="form-control phoneNumber @error('phone') is-invalid @enderror"
                                                       value="{{ old('phone') }}" required>
                                                <input type="hidden" class="fullPhoneNumber" name="phone">
                                                @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                        </div>

                                        <div class="col-md-6 login-form-2">
                                            <label for="password" class="form-label">{{ __('auth.password_input') }} <span class="text-danger">*</span></label>
                                            <div class="eye_show">
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="passwordInput" name="password" required autocomplete="new-password">
                                                <span href="#" class="showPassword fa fa-eye" data-toggle="#passwordInput"></span>
                                            </div>
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 login-form-2">
                                            <label for="password-confirm" class="form-label">{{ __('auth.confirm_password') }} <span class="text-danger">*</span></label>
                                            <div class="eye_show">
                                                <input type="password" class="form-control" id="passwordConfirmInput" name="password_confirmation" required autocomplete="new-password">
                                                <span href="#" class="showPassword fa fa-eye" data-toggle="#passwordConfirmInput"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 login-form-2">
                                                <label for="type" class="form-label">{{ __('auth.type') }} <span class="text-danger">*</span></label>
                                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                                    <option value="factory" @if(old('type') == 'factory') selected @endif>{{__('auth.type_factory')}}</option>
                                                    <option value="distributor" @if(old('type') == 'distributor') selected @endif>{{__('auth.type_distributor')}}</option>
                                                    <option value="wholesales" @if(old('type') == 'wholesales') selected @endif>{{__('auth.type_wholesales')}}</option>
                                                    <option value="retail" @if(old('type') == 'retail') selected @endif>{{__('auth.type_retail')}}</option>
                                                </select>
                                                @error('type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                        </div>
                                        <div class="col-md-6 login-form-2">
                                            <label for="country" class="form-label">{{ __('auth.country') }} <span class="text-danger">*</span></label>
                                            <select class="form-control @error('country_id') is-invalid @enderror" id="country" name="country_id" required>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('country_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 login-form-2">
                                                <label for="address" class="form-label">{{ __('auth.address') }} <span class="text-danger">*</span></label>
                                                <input id="address" type="text" class="form-control rtl_important @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" autocomplete="address" required>
                                                @error('address')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                        </div>
                                        <div class="col-md-12 pt-2">
                                            <div class="js-form-message ">
                                                <label class="form-label" style="font-weight: 500;font-size: 15px;">
                                                    {{ __('vendor.licenses') }}
                                                </label>
                                                <div class="row">
                                                    <div class="col-md-3 imgUp m-0">
                                                        <div class="imagePreview" style="display: none;"></div>
                                                        <label class="btn btn-primary">
                                                            {{ __('vendor.choose_a_file') }} <input type="file" name="licenses[]" class="uploadFile img" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
                                                        </label>
                                                    </div>
                                                    <i class="fa fa-plus imgAdd"></i>
                                                </div>
                                                @if ($errors->has('licenses.*') || $errors->has('licenses'))
                                                    <div class="alert alert-danger">
                                                        <ul role="alert" style="list-style: list-unstyled">
                                                            @if($errors->has('licenses.*'))
                                                                <li>{{ $errors->first('licenses.*') }}</li>
                                                            @else
                                                                <li>{{ $errors->first('licenses') }}</li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12 login-form-4 pt-2">
                                            <input class="checkModal" type="checkbox" name="contract" />&nbsp;
                                            <label class="checkModal" style="cursor: pointer">{{__('auth.contract_check_label')}}</label>
                                            @error('contract')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 login-form-submit-btn pt-2">
                                            <button id="showmap" >{{ __('auth.map') }}</button>
                                            <button id="submit_form" type="submit">{{ __('auth.register') }}</button>
                                        </div>

                                        <input type="hidden" class="lat" value="{{old('latitude')}}" readonly name="latitude" required>
                                        <input type="hidden" class="lng" value="{{old('longitude')}}" readonly name="longitude" required>


{{--
                                        <div class="col-md-12">
                                            <h3 class="font-size-18 mb-3">{{__('auth.signup_headline')}} :</h3>
                                            <ul class="list-group list-group-borderless">
                                                <li class="list-group-item px-0"><i class="fas fa-check mr-2 text-green font-size-16"></i> {{ __('auth.speed_checkout') }}</li>
                                                <li class="list-group-item px-0"><i class="fas fa-check mr-2 text-green font-size-16"></i> {{ __('auth.track_orders') }}</li>
                                                <li class="list-group-item px-0"><i class="fas fa-check mr-2 text-green font-size-16"></i> {{ __('auth.keep_records') }}</li>
                                            </ul>
                                        </div>
--}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="register-l section-login-1"></div>
        </section>
    </div>
</div>

<div class="modal fade" id="termsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 1000px">
        <div class="modal-content st_model_new">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{nova_get_setting_translate('vendor_terms_title')}}</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="modalText">
                            {!! nova_get_setting_translate('vendor_terms_text') !!}
                        </div>
                    </div>
                </div>
                <button type="button" class="modalButton" data-dismiss="modal">{{nova_get_setting_translate('vendor_terms_button')}}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="MapModel" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 1000px">
        <div class="modal-content st_model_new">
            <div class="modal-body" style="padding: 0.5rem;">
                <div id="MapBody" class="modalText" style="min-height: 600px;"> </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('extraScripts')
    <style>
        .modalText{
            overflow-y: auto;
            max-height: 55vh;
        }
        .modalButton{
            border-radius: 5px;
            margin: 30px auto;
            width: auto;
            padding: 15px;
            display: block;
            border: 2px solid #003f91;
            font-weight: bold;
        }
        .login-form-2{
            padding-top: 1rem;
        }
        .section-login-1 .col-md-12{
            height: 100%;
            padding: 0;
        }
        @media screen and (min-width: 480px) {
            #element_map{
                height: 100% !important;
            }
        }
        @media screen and (max-width: 480px) {
            #element_map{
                height: 595px !important;
            }
        }
        #MapBody > * {
            margin: 0;
            padding : 0;
        }

    </style>
    <script defer type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('FRONT_MAPS_KEY')}}&libraries=places&sensor=false"></script>
    <script defer src="{{front_url()}}/assets/js/map.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            $(document).ready(function() {
                if ($(window).width() <= '480'){
                    $("#showmap").css("display", "block");
                    $("#submit_form").css("display", "none");
                }else{
                    $("#showmap").css("display", "none");
                    $("#submit_form").css("display", "block");
                }

                $('.checkModal').on('click', function () {
                    $('#termsModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                });
                $('#showmap').on('click', function () {
                    $('#MapModel').modal({
                        keyboard: false
                    });
                    $("#showmap").css("display", "none");
                    $("#submit_form").css("display", "block");

                });

            });

            // upload Licenses
            $(".imgAdd").click(function(){
                $(this).closest(".row").find('.imgAdd').before('<div class="col-md-3 imgUp"><div class="imagePreview" style="display:none;"></div><label class="btn btn-primary">Upload<input name="licenses[]" type="file" class="uploadFile img" value="Upload Photo" style="width:0px;height:0px;overflow:hidden;"></label><i class="fa fa-times del"></i></div>');
            });
            $(document).on("click", "i.del" , function() {
                $(this).parent().remove();
            });
            $(function() {
                $(document).on("change",".uploadFile", function()
                {
                    var uploadFile = $(this);
                    var files = !!this.files ? this.files : [];
                    if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

                    var reader = new FileReader(); // instance of the FileReader

                    if (/^image/.test( files[0].type)){ // only image file
                        reader.readAsDataURL(files[0]); // read the local file

                        reader.onloadend = function(){ // set image data as background of div
                            //alert(uploadFile.closest(".upimage").find('.imagePreview').length);
                            uploadFile.closest(".imgUp").find('.imagePreview').css({"background-image":"url("+this.result+")", "display":"block"});
                        }
                    } else {
                        reader.readAsDataURL(files[0]); // read the local file

                        reader.onloadend = function(){ // set image data as background of div
                            //alert(uploadFile.closest(".upimage").find('.imagePreview').length);
                            uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url('/public/assets/img/pdf.svg')");
                        }
                    }
                });
            });
            var nav = $('.nav_fixed');
            $(window).scroll(function () {
                if ($(this).scrollTop() > 100) {
                    nav.css({"position":"static"});
                }
            });


            CheckGeolocationSettings();

            function CheckGeolocationSettings() {
                const MapDesign =  '{!! generate_map() !!}';

                const SlogonHolder = '<div class="section-login-1-main">'
                    +'<h1 class="section-login-1-title">{{ __('general.youmats') }}</h1>'
                    +'<p class="section-login-1-text">{{ __('general.slogan') }}</p>'
                        +'<div class="section-login-1-img">'
                            +'<img src="https://rvs-gradie-signup-page.vercel.app/Assets/iPhone-Mockup.png" alt="">'
                        +'</div>'
                 +'</div>';

                navigator.permissions.query({
                    name: 'geolocation'
                }).then(function(result) {
                    $(".section-login-1 .section-login-1-main").remove();
                    $(".section-login-1 .col-md-12").remove();

                    if (result.state == 'granted') {
                        if ($(window).width() <= '480'){
                            $("#MapBody").append(MapDesign);
                        }else{
                            $(".section-login-1").append(MapDesign);
                        }
                    } else {
                        $(".section-login-1").append(SlogonHolder);
                    }
                    result.onchange = function() {
                        CheckGeolocationSettings();
                    }
                });
            }


        });
    </script>
@endsection
