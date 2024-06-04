document.addEventListener('DOMContentLoaded', function() {
    <!-- JS Plugins Init. -->
    $(window).on('load', function () {
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
        afterOpen: function () {
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
        beforeClose: function () {
            $('#hamburgerTrigger').removeClass('is-active');
        },
        afterClose: function() {
            $('#headerSidebarList .collapse.show').collapse('hide');
        }
    });

    $('#headerSidebarList [data-toggle="collapse"]').on('click', function (e) {
        e.preventDefault();

        var target = $(this).data('target');

        if($(this).attr('aria-expanded') === "true") {
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
    $(document).on('click', '.btn-add-cart', function(){
        let url  = $(this).data('url'),
            btn = $(this);

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                _token: '{{ csrf_token() }}',
                quantity: btn.parent().siblings().find('.cart-quantity').val()
            }
        }).done(function(response) {
            $('.cartCount').html(response.count);
            $('.cartTotal').html(response.total);
            toastr.success(response.message);
        }).fail(function(response) {
            toastr.error(response);
        })
    });

    //WISHLIST
    $(".btn-add-wishlist").on('click', function(){
        let url = $(this).data('url');
        $.ajax({
            type: 'POST',
            url: url,
            data: { _token: '{{ csrf_token() }}' }
        })
        .done(function(response) {
            if(response.status)
                toastr.success(response.message);
            else
                toastr.warning(response.message)
        })
        .fail(function(response) {
            toastr.error(response.responseJSON.message ?? 'error');
        })
    });

    let inputs = document.querySelectorAll(".phoneNumber");

    $.each(inputs, function(key, value){
        window.intlTelInput(value, {
            utilsScript: 'public/assets/js/utils.js',
            formatOnDisplay: true,
            // autoPlaceholder: true,
            initialCountry: "auto",
            hiddenInput: "phone",
            separateDialCode: true,
            autoPlaceholder: "polite",
            geoIpLookup: function(success, failure) {
            $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "sa";
                    success(countryCode);
                });
            }
        });
    });

    // initialization of quantity counter
    $.HSCore.components.HSQantityCounter.init('.js-quantity');

    var nav = $('.nav_fixed');
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            nav.addClass("fixed-header");
        } else {
            nav.removeClass("fixed-header");
        }
    });

    const actualBtn = document.getElementById('actual-btn');
    const fileChosen = document.getElementById('file-chosen');
    actualBtn.addEventListener('change', function(){
        fileChosen.textContent = this.files[0].name
    });

    (function(document,window, index) {
        var inputs = document.querySelectorAll('.inputfile');
        Array.prototype.forEach.call(inputs,function(input) {
            var label = input.nextElementSibling,
            labelVal = label.innerHTML;
            input.addEventListener('change', function(e) {
                var fileName = '';
                if( this.files && this.files.length > 1 )
                    fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                else
                    fileName = e.target.value.split('\\').pop();

                if(fileName)
                    label.querySelector( 'span' ).innerHTML = fileName;
                else
                    label.innerHTML = labelVal;
            });

            // Firefox bug fix
            input.addEventListener( 'focus', function(){ input.classList.add('has-focus'); });
            input.addEventListener( 'blur', function(){ input.classList.remove('has-focus'); });
        });
    }(document, window, 0));
});




