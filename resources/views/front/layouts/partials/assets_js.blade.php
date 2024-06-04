<script src="{{ mix('/assets/js/app.min.js') }}"></script>

<!-- moment -->
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"
    integrity="sha512-rmZcZsyhe0/MAjquhTgiUcb4d9knaFc7b5xAfju483gbEXTkeJRUMIPk6s3ySZMYUHEcjKbjLjyddGWMrNEvZg=="
    crossorigin="anonymous"></script>
<script defer src="{{ front_url() }}/assets/js/date.js"></script>

<!-- Toastr JS -->
<script defer src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        toastr.options = {
            "positionClass": "toast-top-center",
        }
    });
</script>

{{-- @if (is_company()) --}}
{{-- <script defer> --}}
{{--    document.addEventListener('DOMContentLoaded', function() { --}}
{{--        (function (w, d, u) { --}}
{{--            var s = d.createElement('script'); --}}
{{--            s.async = true; --}}
{{--            s.src = u + '?' + (Date.now() / 60000 | 0); --}}
{{--            var h = d.getElementsByTagName('script')[0]; --}}
{{--            h.parentNode.insertBefore(s, h); --}}
{{--        })(window, document, 'https://cdn.bitrix24.com/b12855593/crm/site_button/loader_1_sdm5a9.js'); --}}
{{--    }); --}}
{{-- </script> --}}
{{-- @endif --}}

@include('front.layouts.partials.alerts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        $(document).on('click', '.typeIntroduceButton', function() {
            let url = $(this).data('url');
            $.ajax({
                    type: 'GET',
                    url: url
                })
                .done(function(response) {
                    if (response.status) {
                        location.reload();
                    }
                })
        });

        @if ($agent->isDesktop())
            $('.userType-switch').tooltip('show');
            $('.userType-switch').on('mouseover', function() {
                $('.userType-switch').tooltip('dispose');
            });
            setTimeout(function() {
                $('.userType-switch').tooltip('dispose');
            }, 10000);
        @else
            $('.userTypeSwitcher').tooltip('show');
            $('.userTypeSwitcher').on('click', function() {
                $(this).tooltip('dispose');
            });
            setTimeout(function() {
                $('.userTypeSwitcher').tooltip('dispose');
            }, 10000);
        @endif

        //JS Plugins Init.
        $(window).on('load', function() {
            // initialization of HSMegaMenu component
            $('.js-mega-menu').HSMegaMenu({
                event: 'hover',
                direction: 'horizontal',
                pageContainer: $('.container'),
                breakpoint: 767.98,
                hideTimeOut: 0
            });
        });

        // initialization of header
        $.HSCore.components.HSHeader.init($('#header'));

        // initialization of animation
        $.HSCore.components.HSOnScrollAnimation.init('[data-animation]');

        // initialization of unfold component
        $.HSCore.components.HSUnfold.init($('[data-unfold-target]'), {
            afterOpen: function() {
                $(this).find('input[type="search"]').focus();
            }
        });

        // initialization of popups
        $.HSCore.components.HSFancyBox.init('.js-fancybox');

        // initialization of countdowns
        var countdowns = $.HSCore.components.HSCountdown.init('.js-countdown', {
            yearsElSelector: '.js-cd-years',
            monthsElSelector: '.js-cd-months',
            daysElSelector: '.js-cd-days',
            hoursElSelector: '.js-cd-hours',
            minutesElSelector: '.js-cd-minutes',
            secondsElSelector: '.js-cd-seconds'
        });

        // initialization of malihu scrollbar
        $.HSCore.components.HSMalihuScrollBar.init($('.js-scrollbar'));

        // initialization of forms
        $.HSCore.components.HSFocusState.init();

        // initialization of form validation
        $.HSCore.components.HSValidation.init('.js-validate', {
            rules: {
                confirmPassword: {
                    equalTo: '#signupPassword'
                }
            }
        });

        // initialization of forms
        $.HSCore.components.HSRangeSlider.init('.js-range-slider');

        // initialization of show animations
        $.HSCore.components.HSShowAnimation.init('.js-animation-link');

        // initialization of fancybox
        $.HSCore.components.HSFancyBox.init('.js-fancybox');

        // initialization of slick carousel
        $.HSCore.components.HSSlickCarousel.init('.js-slick-carousel');

        // initialization of go to
        $.HSCore.components.HSGoTo.init('.js-go-to');

        // initialization of hamburgers
        $.HSCore.components.HSHamburgers.init('#hamburgerTrigger');

        // initialization of unfold component
        $.HSCore.components.HSUnfold.init($('[data-unfold-target]'), {
            beforeClose: function() {
                $('#hamburgerTrigger').removeClass('is-active');
            },
            afterClose: function() {
                $('#headerSidebarList .collapse.show').collapse('hide');
            }
        });

        $('#headerSidebarList [data-toggle="collapse"]').on('click', function(e) {
            e.preventDefault();

            var target = $(this).data('target');

            if ($(this).attr('aria-expanded') === "true") {
                $(target).collapse('hide');
            } else {
                $(target).collapse('show');
            }
        });

        // initialization of unfold component
        $.HSCore.components.HSUnfold.init($('[data-unfold-target]'));

        // initialization of select picker
        $.HSCore.components.HSSelectPicker.init('.js-select');

        //HANDLE CART
        $(document).on('click', '.btn-add-cart', function() {
            let url = $(this).data('url'),
                delivery_url = $(this).data('delivery-url'),
                btn = $(this);

            $.ajax({
                type: 'POST',
                url: delivery_url,
                data: {
                    _token: '{{ csrf_token() }}',
                    quantity: btn.parent().siblings().find('.cart-quantity').val()
                }
            }).done(function(response) {
                if (!response.status) {
                    if (confirm(response.message)) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                _token: '{{ csrf_token() }}',
                                quantity: btn.parent().siblings().find('.cart-quantity')
                                    .val()
                            }
                        }).done(function(response) {
                            $('.cartCount').html(response.count);
                            $('.cartTotal').html(response.total);
                            if (response.success) {
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        }).fail(function(response) {
                            toastr.error(response);
                        })
                    }
                } else {
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            _token: '{{ csrf_token() }}',
                            quantity: btn.parent().siblings().find('.cart-quantity')
                                .val()
                        }
                    }).done(function(response) {
                        $('.cartCount').html(response.count);
                        $('.cartTotal').html(response.total);
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }).fail(function(response) {
                        toastr.error(response);
                    })
                }
            }).fail(function(response) {
                toastr.error(response);
            })
        });

        //WISHLIST
        {{-- $(".btn-add-wishlist").on('click', function(){ --}}
        {{--    let url = $(this).data('url'); --}}
        {{--    $.ajax({ --}}
        {{--        type: 'POST', --}}
        {{--        url: url, --}}
        {{--        data: { _token: '{{ csrf_token() }}' } --}}
        {{--    }) --}}
        {{--        .done(function(response) { --}}
        {{--            if(response.status) --}}
        {{--                toastr.success(response.message); --}}
        {{--            else --}}
        {{--                toastr.warning(response.message) --}}
        {{--        }) --}}
        {{--        .fail(function(response) { --}}
        {{--            toastr.error(response.responseJSON.message ?? 'error'); --}}
        {{--        }) --}}
        {{-- }); --}}

        const input = document.querySelector(".phoneNumber");
        const output = document.querySelector(".fullPhoneNumber");

        let iti = window.intlTelInput(input, {
            initialCountry: "sa",
            nationalMode: true,
            utilsScript: '{{ front_url() }}/assets/js/utils.js',
            preferredCountries: ['sa'],
            excludeCountries: ['il'],
            separateDialCode: true,
            formatOnDisplay: true
        });

        const handleChange = () => {
            output.value = iti.s.dialCode + input.value;
        };

        // listen to "keyup", but also "change" to update when the user selects a country
        input.addEventListener('change', handleChange);
        input.addEventListener('keyup', handleChange);

        // initialization of quantity counter
        $.HSCore.components.HSQantityCounter.init('.js-quantity');

        var nav = $('.nav_fixed');
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                nav.addClass("fixed-header");
            } else {
                nav.removeClass("fixed-header");
            }
        });

        const actualBtn = document.getElementById('actual-btn');
        const fileChosen = document.getElementById('file-chosen');
        actualBtn.addEventListener('change', function() {
            fileChosen.textContent = this.files[0].name
        });

        (function(document, window, index) {
            var inputs = document.querySelectorAll('.inputfile');
            Array.prototype.forEach.call(inputs, function(input) {
                var label = input.nextElementSibling,
                    labelVal = label.innerHTML;
                input.addEventListener('change', function(e) {
                    var fileName = '';
                    if (this.files && this.files.length > 1)
                        fileName = (this.getAttribute('data-multiple-caption') || '')
                        .replace('{count}', this.files.length);
                    else
                        fileName = e.target.value.split('\\').pop();

                    if (fileName)
                        label.querySelector('span').innerHTML = fileName;
                    else
                        label.innerHTML = labelVal;
                });

                // Firefox bug fix
                input.addEventListener('focus', function() {
                    input.classList.add('has-focus');
                });
                input.addEventListener('blur', function() {
                    input.classList.remove('has-focus');
                });
            });
        }(document, window, 0));
    });


    function TrigerWhatsapp(VendorName, ProductLink, CategoryName, VendorPhoneNumber, WhatsappLink,
        VendorHasEncryption, enable_whatsapp_redirect) {

        $(document).ready(function() {

            if ($(window).width() > '480') {

                if (Boolean(parseInt(enable_whatsapp_redirect)) === false) {
                    document.getElementById("DrawingDiv").innerHTML =
                        "<div id='whatsapp-chat' class='show'>" +
                        "<div class='header-chat'>" +
                        "<div class='head-home'>" +
                        "<div class='info-avatar'><img src='https://files.elfsight.com/storage/9274ed8b-a2e8-4cf8-a4cf-fad383377f2b/7b75090c-19a2-452b-9d6b-c2a51ad4916f.jpeg' /></div>" +
                        "<p><span class='whatsapp-name'>Youmats - Vendor</span><br><small>Typically replies within an hour</small></p>" +
                        "</div>" +
                        "<div class='get-new hide'>" +
                        "<div id='get-label'></div>" +
                        "<div id='get-nama'></div>" +
                        "</div>" +
                        "</div>" +
                        "<div class='home-chat'>" +
                        "</div>" +
                        "<div class='start-chat'>" +
                        "<div pattern='https://elfsight.com/assets/chats/patterns/whatsapp.png' class='WhatsappChat__Component-sc-1wqac52-0 whatsapp-chat-body'>" +
                        "<div class='WhatsappChat__MessageContainer-sc-1wqac52-1 dAbFpq'>" +
                        "<div style='opacity: 0;' class='WhatsappDots__Component-pks5bf-0 eJJEeC'>" +
                        "<div class='WhatsappDots__ComponentInner-pks5bf-1 hFENyl'>" +
                        "<div class='WhatsappDots__Dot-pks5bf-2 WhatsappDots__DotOne-pks5bf-3 ixsrax'></div>" +
                        "<div class='WhatsappDots__Dot-pks5bf-2 WhatsappDots__DotTwo-pks5bf-4 dRvxoz'></div>" +
                        "<div class='WhatsappDots__Dot-pks5bf-2 WhatsappDots__DotThree-pks5bf-5 kXBtNt'></div>" +
                        "</div>" +
                        "</div>" +
                        "<div style='opacity: 1;' class='WhatsappChat__Message-sc-1wqac52-4 kAZgZq'>" +
                        "<div class='WhatsappChat__Author-sc-1wqac52-3 bMIBDo'>Youmats - Vendor</div>" +
                        "<div class='WhatsappChat__Text-sc-1wqac52-2 iSpIQi'>Hi there ...<br><br>How can I help you?</div>" +
                        "<div class='WhatsappChat__Time-sc-1wqac52-5 cqCDVm'>1:40</div>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "<div class='blanter-msg'>" +
                        "<textarea id='chat-input' placeholder='Write a response' maxlength='120' row='1'></textarea>" +
                        "</div>" +
                        "</div>" +
                        "<div id='get-number'></div>" +
                        "</div>" +

                        "<div id='add-number' class='show' style='height: 298px;background: hwb(0deg 0% 100% / 70%);'>" +
                        "<div style='padding-top: 30px;text-align: center;'>" +
                        "<div style='width:200px;text-align: center;margin: 0 70px;'>" +
                        "<div class='input-group my-group'>" +
                        "<img loading='eager' class ='img-fluid' style='border:5px solid white;' src='" +
                        "{{ $staticImages->getFirstMediaUrlOrDefault(WHATSAPP_QR_CODE_PATH, 'size_250_250')['url'] }} " +
                        "' >" +
                        "</p>" +
                        "</div>" +
                        "</div>" +
                        "<p style='color: white;font-weight: bold;font-size: large;margin-top: 15px;'>{{ __('general.please_scan_whatsapp_code') }}" +
                        "</div>" +
                        "<a style='z-index:99999;' class='close-chat' href='javascript:void'>x</a>" +
                        "</div>" +
                        "<div id='Thanks-message' class='show' style='display:none;'></div>";

                } else {

                    document.getElementById("DrawingDiv").innerHTML =
                        "<div id='whatsapp-chat' class='show'>" +
                        "<div class='header-chat'>" +
                        "<div class='head-home'>" +
                        "<div class='info-avatar'><img src='https://files.elfsight.com/storage/9274ed8b-a2e8-4cf8-a4cf-fad383377f2b/7b75090c-19a2-452b-9d6b-c2a51ad4916f.jpeg' /></div>" +
                        "<p><span class='whatsapp-name'>Youmats - Vendor</span><br><small>Typically replies within an hour</small></p>" +
                        "</div>" +
                        "<div class='get-new hide'>" +
                        "<div id='get-label'></div>" +
                        "<div id='get-nama'></div>" +
                        "</div>" +
                        "</div>" +
                        "<div class='home-chat'>" +
                        "</div>" +
                        "<div class='start-chat'>" +
                        "<div pattern='https://elfsight.com/assets/chats/patterns/whatsapp.png' class='WhatsappChat__Component-sc-1wqac52-0 whatsapp-chat-body'>" +
                        "<div class='WhatsappChat__MessageContainer-sc-1wqac52-1 dAbFpq'>" +
                        "<div style='opacity: 0;' class='WhatsappDots__Component-pks5bf-0 eJJEeC'>" +
                        "<div class='WhatsappDots__ComponentInner-pks5bf-1 hFENyl'>" +
                        "<div class='WhatsappDots__Dot-pks5bf-2 WhatsappDots__DotOne-pks5bf-3 ixsrax'></div>" +
                        "<div class='WhatsappDots__Dot-pks5bf-2 WhatsappDots__DotTwo-pks5bf-4 dRvxoz'></div>" +
                        "<div class='WhatsappDots__Dot-pks5bf-2 WhatsappDots__DotThree-pks5bf-5 kXBtNt'></div>" +
                        "</div>" +
                        "</div>" +
                        "<div style='opacity: 1;' class='WhatsappChat__Message-sc-1wqac52-4 kAZgZq'>" +
                        "<div class='WhatsappChat__Author-sc-1wqac52-3 bMIBDo'>Youmats - Vendor</div>" +
                        "<div class='WhatsappChat__Text-sc-1wqac52-2 iSpIQi'>Hi there ...<br><br>How can I help you?</div>" +
                        "<div class='WhatsappChat__Time-sc-1wqac52-5 cqCDVm'>1:40</div>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "<div class='blanter-msg'>" +
                        "<textarea id='chat-input' placeholder='Write a response' maxlength='120' row='1'></textarea>" +
                        "</div>" +
                        "</div>" +
                        "<div id='get-number'></div>" +
                        "<a style='z-index:99999;' class='close-chat' href='javascript:void'>x</a>" +
                        "</div>" +

                        "<div id='add-number' class='show' style='height: 298px;background: hwb(0deg 0% 100% / 70%);'>" +
                        "<div style='padding-top: 100px;text-align: center;'>" +
                        "<p style='color: white;font-weight: bold;font-size: large;margin-bottom: 15px;'>{{ __('general.insert_you_phone_number') }}" +
                        "</p>" +
                        "<div style='width:200px;text-align: center;margin: 0 70px;'>" +
                        "<div class='input-group my-group'>" +
                        "<select id='phone_number_code' class='form-control' style='padding: 0;border-radius: 10px 0 0 10px!important;'>" +
                        "<option value='213'>(+213)</option>" +
                        "<option value='973'>(+973)</option>" +
                        "<option value='269'>(+269)</option>" +
                        "<option value='253'>(+253)</option>" +
                        "<option value='20'>(+20)</option>" +
                        "<option value='964'>(+964)</option>" +
                        "<option value='962'>(+962)</option>" +
                        "<option value='965'>(+965)</option>" +
                        "<option value='961'>(+961)</option>" +
                        "<option value='218'>(+218)</option>" +
                        "<option value='222'>(+222)</option>" +
                        "<option value='212'>(+212)</option>" +
                        "<option value='968'>(+968)</option>" +
                        "<option value='970'>(+970)</option>" +
                        "<option value='974'>(+974)</option>" +
                        "<option value='966' selected>(+966)</option>" +
                        "<option value='252'>(+252)</option>" +
                        "<option value='249'>(+249)</option>" +
                        "<option value='963'>(+963)</option>" +
                        "<option value='216'>(+216)</option>" +
                        "<option value='971'>(+971)</option>" +
                        "<option value='967'>(+967)</option>" +
                        "</select>" +
                        "<input type='tel' id='phone_number' class='form-control' style='border-radius: 0 10px 10px 0!important;width: 35%;' name='snpid' maxlength='9' placeholder='(5XX) XXX XXX'/>" +
                        "</div>" +
                        "<input class='w-full btn btn-default btn-primary hover:bg-primary-dark' style='margin: 5% 35%;' type='submit' onclick='submit_message()' value='{{ __('general.submit') }}'>" +
                        "</div>" +
                        "</div>" +
                        "<a style='z-index:99999;' class='close-chat' href='javascript:void'>x</a>" +
                        "</div>" +
                        "<div id='Thanks-message' class='show' style='height: 296px;background: hwb(0deg 0% 100% / 70%);display:none;'>" +
                        "<div style='padding-top: 150px;text-align: center;'>" +
                        "<p id='display_message' style='color: white;font-weight: bold;font-size: large;margin-bottom: 15px;'>" +
                        "{{ __('general.whatsapp_group_was_created') }}" +
                        "</p>" +
                        "</div>" +
                        "</div>" +
                        "<input id='VendorName' type='hidden' value='" + VendorName + "'>" +
                        "<input id='ProductLink' type='hidden' value='" + ProductLink + "'>" +
                        "<input id='CategoryName' type='hidden' value='" + CategoryName + "'>" +
                        "<input id='VendorPhoneNumber' type='hidden' value='" + VendorPhoneNumber + "'>" +
                        "<input id='VendorHasEncryption' type='hidden' value='" + VendorHasEncryption + "'>";

                }

            } else {
                window.open(WhatsappLink, "_blank")
            }

        });
    }

    $(document).on('change', '#phone_number_code', function() {
        if ($('#phone_number_code').val() == "20") {
            $("#phone_number").attr('maxlength', '10');
        } else {
            $("#phone_number").attr('maxlength', '9');
        }
    });

    function submit_message() {

        let phone_number = document.getElementById("phone_number_code").value + document.getElementById("phone_number")
            .value;
        const regex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;

        if (regex.test(phone_number)) {

            document.getElementById("add-number").style.display = 'none';

            let VendorName = document.getElementById("VendorName").value;
            let ProductLink = document.getElementById("ProductLink").value;
            let CategoryName = document.getElementById("CategoryName").value;
            let VendorPhoneNumber = document.getElementById("VendorPhoneNumber").value;
            let VendorHasEncryption = document.getElementById("VendorHasEncryption").value;


            if (VendorPhoneNumber.includes("+")) {
                VendorPhoneNumber = VendorPhoneNumber.replace("+", "u");
            } else {
                VendorPhoneNumber = "u" + VendorPhoneNumber;
            }
            if (phone_number.includes("+")) {
                phone_number = phone_number.replace("+", "u");
            } else {
                phone_number = "u" + phone_number;
            }


            var chat_input = ProductLink + " \n,\n" +
                ";;" + VendorPhoneNumber + ";;\n" +
                ",\n" +
                ";;" + VendorName + ";;\n" +
                ",\n" +
                ";;" + CategoryName + ";;\n" +
                ",\n" +
                ";;" + phone_number + ";;";

            if (VendorHasEncryption == true && $(window).width() > '480') {
                var chat_input = ProductLink + " \n,\n" +
                    ";;" + VendorPhoneNumber + ";;\n" +
                    ",\n" +
                    ";;" + VendorName + ";;\n" +
                    ",\n" +
                    ";;" + CategoryName + ";;\n" +
                    ",\n" +
                    ";;" + phone_number + ";;\n" +
                    ",\n" +
                    ";;pc;;";
            }

            $.ajax({
                url: "https://api.ultramsg.com/instance58289/messages/chat",
                type: 'GET',
                data: {
                    token: "c8m7bwsowe1whxz4",
                    to: "966502111754",
                    body: chat_input,
                    priority: "10"
                }

            }).done(function(data) {

                document.getElementById("chat-input").value = "";
                document.getElementById("Thanks-message").style.display = 'block';

            }).error(function() {

                document.getElementById("chat-input").value = "";
                document.getElementById("Thanks-message").style.display = 'block';
                document.getElementById('display_message').innerText =
                    "{{ __('general.there_is_a_whatsapp_error') }}";

            })

        }


    }
    $(document).on("click", ".informasi", function() {
            (document.getElementById("get-number").innerHTML = $(this)
                .children(".my-number")
                .text()),
            $(".start-chat,.get-new")
                .addClass("show")
                .removeClass("hide"),
                $(".home-chat,.head-home")
                .addClass("hide")
                .removeClass("show"),
                (document.getElementById("get-nama").innerHTML = $(this)
                    .children(".info-chat")
                    .children(".chat-nama")
                    .text()),
                (document.getElementById("get-label").innerHTML = $(this)
                    .children(".info-chat")
                    .children(".chat-label")
                    .text());
        }),
        $(document).on("click", ".close-chat", function() {
            $("#whatsapp-chat").addClass("hide").removeClass("show");
            document.getElementById("Thanks-message").style.display = 'none';
            $("#add-number").addClass("hide").removeClass("show");
        })
</script>

@include('front.layouts.partials.ajax')
@yield('extraScripts')
