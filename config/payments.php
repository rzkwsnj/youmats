<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /**
     * the route used to post payment data
     */
    'routes' => [
        'submit-payment' => '/checkout/submit-payment',
        'form-route' => '/checkout/payment',
    ],

    'payfort' => [

        'callback_urls' => [
            'error-page' => '/checkout/error',
            'success-page' => '/checkout/success',
        ],
        'sandboxMode' => env('PAYFORT_SAND_BOX_MODE', true),

        /**
         * language used to specify the response language returned from payfort
         */
        'language' => env('PAYFORT_LANGUAGE', 'en'),

        /**
         * your Merchant Identifier account (mid)
         */
        'merchantIdentifier' => env('PAYFORT_MERCHANT_IDENTIFIER', ''),

        /**
         * your access code
         */
        'accessCode' => env('PAYFORT_ACCESS_CODE', ''),

        /**
         * SHA Request passphrase
         */
        'SHARequestPhrase' => env('PAYFORT_SHA_REQUEST_PHRASE', ''),

        /**
         * SHA Response passphrase
         */
        'SHAResponsePhrase' => env('PAYFORT_SHA_RESPONSE_PHRASE', ''),

        /**
         * SHA Type (Hash Algorith)
         * expected Values ("sha1", "sha256", "sha512")
         */
        'SHAType' => env('PAYFORT_SHA_TYPE', 'sha256'),

        /**
         * command
         * expected Values ("AUTHORIZATION", "PURCHASE")
         */
        'command' => env('PAYFORT_COMMAND', 'AUTHORIZATION'),

        /**
         * order currency
         */
        'currency'   => env('PAYFORT_CURRENCY', 'SAR'),
    ],

    /**
     *
     *  payfort Apple Pay configuration
     *
     */

    'payfort_apple_pay' => [

        'sandboxMode'           => true,
        'language'              => 'ar',
        'merchantIdentifier'    => '',
        'accessCode'            => '',
        'SHARequestPhrase'      => '',
        'SHAResponsePhrase'     => '',
        'SHAType'     => 'sha256',
        'command' => 'PURCHASE',
        'currency' => 'SAR',

    ]
];
