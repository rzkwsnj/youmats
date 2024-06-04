<?php

// define link for uploads folders
define("USER_PROFILE", 'user_profile');
define("USER_COVER", 'user_cover');
define("COMPANY_PATH", 'company');
define("LANGUAGE_PATH", 'language');
define("CATEGORY_PATH", 'category');
define("CATEGORY_COVER", 'category_cover');
define("TEAM_PATH", 'team');
define("PRODUCT_PATH", 'product');
define("ARTICLE_PATH", 'article');
define("GENERATE_PRODUCT_PATH", 'generate_product');
define("VENDOR_LOGO", 'vendor_logo');
define("VENDOR_COVER", 'vendor_cover');
define("VENDOR_PATH", 'vendor');
define("PARTNER_PATH", 'partner');
define("CURRENCY_PATH", 'currency');
define("PAGE_PATH", 'page');
define("SLIDER_PATH", 'slider');
define("DRIVER_PHOTO", 'driver_photo');
define("DRIVER_ID", 'driver_id');
define("DRIVER_LICENSE", 'driver_license');
define("CAR_PHOTO", 'car_photo');
define("CAR_LICENSE", 'car_license');
define("INQUIRE_PATH", 'inquire');
define('QUOTE_ATTACHMENT', 'quote');

define('LOGO_PATH', 'logo');
define('FAVICON_PATH', 'favicon');
define('SLIDER_BACKGROUND_PATH', 'slider_background');
define('HOME_FIRST_SECTION_PATH', 'home_first_section');
define('HOME_SECOND_SECTION_PATH', 'home_second_section');
define('HOME_THIRD_SECTION_PATH', 'home_third_section');
define('WHATSAPP_QR_CODE_PATH', 'whatsapp_qr_code');


// define validation
define('REQUIRED_VALIDATION', ['required']);
define('NULLABLE_VALIDATION', ['nullable']);
// String
define('REQUIRED_STRING_VALIDATION', ['required', 'string', 'max:191']);
define('NULLABLE_STRING_VALIDATION', ['nullable', 'string', 'max:191']);
define('REQUIRED_TEXT_VALIDATION', ['required', 'string']);
define('NULLABLE_TEXT_VALIDATION', ['nullable', 'string']);

//url
define('NULLABLE_URL_VALIDATION', ['nullable', 'string', 'max:191', 'url']);
define('REQUIRED_URL_VALIDATION', ['required', 'string', 'max:191', 'url']);

// Integer
define('REQUIRED_INTEGER_VALIDATION', ['required', 'integer']);
define('NULLABLE_INTEGER_VALIDATION', ['nullable', 'integer']);
define('REQUIRED_NUMERIC_VALIDATION', ['required', 'numeric']);
define('NULLABLE_NUMERIC_VALIDATION', ['nullable', 'numeric']);

// Password
define('REQUIRED_PASSWORD_VALIDATION', ['required', 'string', 'min:8', 'confirmed']);
define('NULLABLE_PASSWORD_VALIDATION', ['nullable', 'string', 'min:8', 'confirmed']);
define('REQUIRED_PASSWORD_NOT_CONFIRMED_VALIDATION', ['required', 'string', 'min:8']);


// Image
define('REQUIRED_IMAGE_VALIDATION', ['required', 'image', 'mimes:jpg,jpeg,png,gif,svg,webp', 'max:2048']);
define('NULLABLE_IMAGE_VALIDATION', ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,svg,webp', 'max:2048']);

define('REQUIRED_PDF_VALIDATION', ['required', 'file', 'mimes:pdf', 'max:4096']);
define('NULLABLE_PDF_VALIDATION', ['nullable', 'file', 'mimes:pdf', 'max:4096']);

define('REQUIRED_FILE_VALIDATION', ['required', 'file', 'mimes:png,jpg,jpeg,pdf,doc,docx,txt,csv,xlsx,svg,webp', 'max:10000']);
define('NULLABLE_FILE_VALIDATION', ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf,doc,docx,txt,csv,xlsx,svg,webp', 'max:10000']);

define('REQUIRED_EXCEL_VALIDATION', ['required', 'file', 'mimes:xlsx,csv,xls']);
// Array
define('ARRAY_VALIDATION', ['array']);
define('REQUIRED_ARRAY_VALIDATION', ['required', 'array']);

// Date
define('REQUIRED_DATE_VALIDATION', ['required', 'date']);
define('NULLABLE_DATE_VALIDATION', ['nullable', 'date']);

// Email
define('REQUIRED_EMAIL_VALIDATION', ['required', 'email', 'max:191']);
