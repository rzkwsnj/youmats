@extends('front.layouts.master')
@section('metaTags')
    <title>YouMats | Contact Us</title>
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
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">{{ __('contact.contact_us') }}</li>
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
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3628.186227536906!2d46.626914815372984!3d24.582768462384305!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2f1980f4b680ed%3A0xc0cf70a369cd9702!2z2YrZiNmF2KfYqtizIHwg2YXZiNmC2Lkg2YXZiNin2K8g2KfZhNio2YbYp9ihICIgWW91bWF0cywgQnVpbGRpbmcgTWF0ZXJpYWxzICI!5e0!3m2!1sen!2seg!4v1658777098234!5m2!1sen!2seg" width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                </div>
            </div>
        </div>

        <div class="row mb-10">
            <div class="col-md-8 col-xl-9">
                <div class="mr-xl-6">
                    <div class="border-bottom border-color-1 mb-5">
                        <h3 class="section-title mb-0 pb-2 font-size-25">{{ __('contact.leave_a_message') }}</h3>
                    </div>
                    <p class="max-width-830-xl text-gray-90">{{ __('contact.description') }}</p>
                    <form id="contactForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Input -->
                                <div class="js-form-message mb-4">
                                    <label class="form-label">
                                        {{ __('contact.name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}">
                                </div>
                                <!-- End Input -->
                            </div>
                            <div class="col-md-6">
                                <!-- Input -->
                                <div class="js-form-message mb-4">
                                    <label class="form-label">
                                        {{ __('contact.email') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}">
                                </div>
                                <!-- End Input -->
                            </div>
                            <div class="col-md-6">
                                <!-- Input -->
                                <div class="js-form-message mb-4">
                                    <label for="phone" class="form-label">
                                        {{ __('contact.phone') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" id="phone" class="form-control phoneNumber @error('phone') is-invalid @enderror"
                                           value="{{ old('phone') }}" required>
                                    <input type="hidden" class="fullPhoneNumber" name="phone">
                                </div>
                                <!-- End Input -->
                            </div>
                            <div class="col-md-12">
                                <div class="js-form-message mb-4">
                                    <label class="form-label">
                                        {{ __('contact.message') }}
                                    </label>
                                    <div class="input-group">
                                        <textarea class="form-control p-5 @error('message') is-invalid @enderror" rows="4" name="message"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary-dark-w px-5">{{ __('contact.send_message') }}</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4 col-xl-3">
                <div class="border-bottom border-color-1 mb-5">
                    <h3 class="section-title mb-0 pb-2 font-size-25">{{ __('contact.our_store') }}</h3>
                </div>
                <div class="mr-xl-6">
                    <address class="mb-6">
                        {{__('info.address')}}
                    </address>
                    <h5 class="font-size-14 font-weight-bold mb-3">{{ __('contact.hours_of_operation') }}</h5>
                    <ul class="list-unstyled mb-6">
                        <li class="flex-center-between mb-1"><span class="">{{__('contact.saturday')}}:</span><span class="">{{__('contact.saturday_time')}}</span></li>
                        <li class="flex-center-between"><span class="">{{__('contact.sunday')}}</span><span class="">{{__('contact.sunday_time')}}</span></li>
                        <li class="flex-center-between mb-1"><span class="">{{__('contact.monday')}}:</span><span class="">{{__('contact.monday_time')}}</span></li>
                        <li class="flex-center-between mb-1"><span class="">{{__('contact.tuesday')}}:</span><span class="">{{__('contact.tuesday_time')}}</span></li>
                        <li class="flex-center-between mb-1"><span class="">{{__('contact.wednesday')}}:</span><span class="">{{__('contact.wednesday_time')}}</span></li>
                        <li class="flex-center-between mb-1"><span class="">{{__('contact.thursday')}}:</span><span class="">{{__('contact.thursday_time')}}</span></li>
                        <li class="flex-center-between mb-1"><span class="">{{__('contact.friday')}}:</span><span class="">{{__('contact.friday_time')}}</span></li>
                    </ul>
                    <h5 class="font-size-14 font-weight-bold mb-3">{{ __('contact.careers') }}</h5>
                    <p class="text-gray-90">{{ __('contact.careers_message') }}: <a class="text-blue text-decoration-on" href="mailto:{{__('info.email')}}">{{__('info.email')}}</a></p>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('extraScripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // contactForm Request
            var form = $("#contactForm"),
                button = $("#contactForm button"),
                buttonContent = button.text();
            form.submit(function (e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: "{{route('front.contact.request')}}",
                    data: $(this).serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        button.attr('disabled', true);
                        button.html('<i class="fa fa-spinner fa-spin"></i>');
                    },
                    success: function (response) {
                        if (response.status) {
                            toastr.success(response.message);
                            form.find("input, textarea").val("");
                        } else
                            toastr.warning(response.message)

                        button.attr('disabled', false);
                        button.text(buttonContent);
                        form.reset();
                        // console.log(response);
                    },
                    error: function (response) {
                        // toastr.error(response.responseJSON.message);
                        let errors = response.responseJSON.errors;

                        $.each(errors, function (key, value) {
                            toastr.error(value, key);
                        })
                        button.attr('disabled', false);
                        button.text(buttonContent);
                    }
                });
            });
        });
    </script>
@endsection
