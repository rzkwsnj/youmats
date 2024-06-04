@extends('front.layouts.master')
@section('metaTags')
    <title>YouMats | Register</title>
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
                    <h1 class="section-login-2-title">{{ __('auth.register') }}</h1>
                <div class="row">
                    <div class="col-md-12">
                        <div class="tab-content" id="Jpills-tabContent">
                            <div class="tab-pane fade active show" id="Jpills-one-example1" role="tabpanel" aria-labelledby="Jpills-one-example1-tab">
                                <form method="POST" class="section-login-2-form mt-1" action="{{ route('register') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 login-form-1">
                                                <label for="name" class="form-label">{{ __('auth.name') }} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control rtl_important @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                                @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                        </div>
                                        <div class="col-md-6 login-form-1">
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
                                            <label for="address" class="form-label">{{ __('auth.address') }} <span class="text-danger">*</span></label>
                                            <input id="address" type="text" class="form-control rtl_important @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" autocomplete="address" required>
                                            @error('address')
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

                                        <div class="col-md-12">
                                            <div class="js-form-message form-group mt-5">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="select_reg active" for="registerAsIndividual">
                                                            <input type="radio" name="type" value="individual" id="registerAsIndividual">
                                                            {{ __('auth.register_as_individual') }}
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="select_reg" for="registerAsCompany">
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
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="documents"></div>
                                        @error('files')
                                        <div class="alert alert-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                        @enderror

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
                $('#showmap').on('click', function () {
                    $('#MapModel').modal({
                        keyboard: false
                    });
                    $("#showmap").css("display", "none");
                    $("#submit_form").css("display", "block");

                });

            });

            $(function(){
                $('#registerAsIndividual').attr('checked', 'checked');
            });
            $('.select_reg').on('click', function () {
                $('.select_reg').removeClass('active');
                $(this).addClass('active');
            });

            $(document).ready(function() {
                $("ul.nav-tab > li > a").on('shown.bs.tab', function(e) {
                    window.location.hash = $(e.target).attr('id');
                })

                $("#registerAsCompany").click(function(e){
                    $("#documents").html('');
                    $("#documents").html(`
                    <div class="row" bis_skin_checked="1">
                        <div class="col-md-3 imgUp m-0" bis_skin_checked="1">
                            <div class="imagePreview" style="display: none;" bis_skin_checked="1"></div>
                            <label class="btn btn-primary">
                                {{ __('auth.documents') }} <input type="file" name="files[]" class="uploadFile img" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
                            </label>
                        </div>
                    </div>
                    `);
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
                $("#registerAsIndividual").click(function(e){
                    $("#documents").html('');
                });

                let hash = window.location.hash;
                $('ul.nav-tab a[id="'+ hash + '"]').tab('show');
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
