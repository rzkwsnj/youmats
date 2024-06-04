@extends('front.layouts.master')
@section('metaTags')
    <title>{{env('APP_NAME')}} | {{__('general.payment')}}</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.7.14/vue.min.js" integrity="sha512-BAMfk70VjqBkBIyo9UTRLl3TBJ3M0c6uyy2VMUrq370bWs7kchLNN9j1WiJQus9JAJVqcriIUX859JOm12LWtw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/v-mask/2.2.1/v-mask.min.js" integrity="sha512-7oTKsUd6KxNwNXCNdKm88VZRJMGK5NU5GKBf/Ia0xt/5gAAV63sVXsEXi7DHbkva/C7C5LGAR3IU1BmGUMkA0Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.15/lodash.min.js" integrity="sha512-3oappXMVVac3Ge3OndW0WqpGTWx9jjRJA8SXin8RxmPfc8rg87b31FGy14WHG/ZMRISo9pBjezW9y00RYAEONA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="stylesheet" href="{{front_url()}}/assets/css/payment.css">
@endsection
@section('content')
<div class="payment-box">
<div id="app" class="card mt-50 mb-50">
    <div class="card-title"> {{__('checkout.payment_title')}} </div>
    <form> <span id="card-header">{{__('checkout.product_list')}}</span>
        <div class="row row-1 rtl">
            <div class="col-9">
                {{$membership->name . ' - ' . $category->name}} <b>({{__('general.sar')}} {{$membership->price}})</b>
            </div>
            <div class="col-3 d-flex justify-content-center">
                {{__('general.sar')}} {{$membership->price}}
            </div>
        </div>
        <div class="row row-1 rtl">
            <div class="col-9">
                {{ __('cart.total') }}
            </div>
            <div class="col-3 d-flex justify-content-center">
                {{__('general.sar')}} {{round($membership->price)}}
            </div>
        </div>
        <span id="card-header rtl">{{__('checkout.payment_card')}}</span>
        <div class="row-1">
            <div class="row row-2"> <span id="card-inner">{{__('checkout.holder_name')}}</span> </div>
            <div class="row row-2"> <input v-model="hold_name" :class="{'border-red-500': errors['hold_name']}" id="grid-first-name" type="text" placeholder="Your name"> </div>
            <p class="text-red-500 text-xs italic" v-if="errors['hold_name']">
                @{{errors['hold_name'][0]}}
            </p>
        </div>
        <div class="row-1">
            <div style="position: relative">
                <div class="row row-2"> <span id="card-inner">{{__('checkout.card_number')}}</span> </div>
                <div class="row row-2">
                    <div class="card-type"></div>
                    <input type="text"
                           v-model="card_number"
                           :class="{'border-red-500': errors['card_number']}"
                           id="grid-password" placeholder="0000 0000 0000 0000"
                           onkeyup="$cc.validate(event)">
                    <div class="card-valid">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
            <p class="text-red-500 text-xs italic" v-if="errors['card_number']">
                @{{errors['card_number'][0]}}
            </p>
        </div>
        <div class="row">
            <div style="width: 48%;margin-right: 4%;">
                <div class="row-1">
                    <input type="text" v-mask="'##/##'" v-model="expiration_date" :class="{'border-red-500': errors['expiration_date']}" id="grid-city" placeholder="Exp.">
                    <p class="text-red-500 text-xs italic" v-if="errors['expiration_year'] || errors['expiration_month']">
                        @{{errors['expiration_year'][0] || errors['expiration_month'][0] }}
                    </p>
                </div>
            </div>
            <div style="width: 48%;">
                <div class="row-1">
                    <input type="text" v-mask="'###'" v-model="cvc" id="grid-zip" :class="{'border-red-500': errors['cvc']}" placeholder="CVV">
                    <p class="text-red-500 text-xs italic" v-if="errors['cvc']">
                        @{{errors['cvc'][0]}}
                    </p>
                </div>
            </div>
        </div>
        <span id="card-header">{{__('checkout.save_card_notes')}}</span>
        <button @click="submitForm" :disabled="loading" :class="{'loading': loading}" class="btn d-flex mx-auto" style="cursor: pointer">
            <b v-if="loading">{{__('checkout.loading')}}</b>
            <b v-else>{{__('checkout.payment_submit_button')}}</b>
        </button>
    </form>
</div>
</div>
<link rel="stylesheet" href="{{front_url()}}/assets/css/cardValidator.css" />
<script src="{{front_url()}}/assets/js/cardValidator.js" type="text/javascript"></script>
<script>
    Vue.directive("mask", VueMask.VueMaskDirective);
    new Vue({
        el: "#app",
        data: {
            expiration_date: "",
            hold_name: "",
            email: "{{optional(auth('vendor')->user())->email ?? 'info@youmats.com'}}",
            cvc: "",
            card_number: "",
            amount: {{round($membership->price)}},
            errors: {},
            loading: false,
        },
        computed: {
            card_number_filter() {
                return _.replace(this.card_number, / /g, "");
            },
            expiration_date_after() {
                return _.replace(this.expiration_date, "/", "");
            },
            expiration_year() {
                return this.expiration_date_after.substring(2, 4);
            },
            expiration_month() {
                return this.expiration_date_after.substring(0, 2);
            },
        },
        methods: {
            submitForm() {
                this.loading = true;
                let endpoint = "{{route('vendor.subscribe.submit')}}"
                let {
                    card_number_filter,
                    expiration_year,
                    expiration_month,
                    cvc,
                    email,
                    amount,
                    hold_name,
                } = this;
                axios
                    .post(endpoint, {
                        card_number_filter,
                        expiration_year,
                        expiration_month,
                        cvc,
                        email,
                        amount,
                        hold_name,
                    })
                    .then(({data}) => {
                        const paymentWrapper = document.createElement(
                            "div"
                        );
                        paymentWrapper.innerHTML = data.form;
                        document.body.append(paymentWrapper);
                        const payfortForm = document.getElementById(
                            "payfort_payment_form"
                        );
                        const params = {
                            card_holder_name: this.hold_name,
                            card_number: `${this.card_number_filter}`,
                            expiry_date: `${this.expiration_year}${this.expiration_month}`,
                            card_security_code: cvc,
                        };
                        for (const param in params) {
                            /* eslint-disable no-prototype-builtins */
                            if (params.hasOwnProperty(param)) {
                                const value = params[param];
                                const input = document.createElement(
                                    "input"
                                );
                                input.type = "hidden";
                                input.id = param;
                                input.name = param;
                                input.value = value;
                                payfortForm.appendChild(input);
                            }
                        }
                        payfortForm.submit.click();
                    })
                    .catch((error) => {
                        this.loading = false;
                        if (error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        }
                    });
            },
        },
    });
</script>
@endsection
