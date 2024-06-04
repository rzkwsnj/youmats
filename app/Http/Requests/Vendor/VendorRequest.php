<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name_en' => REQUIRED_STRING_VALIDATION,
            'name_ar' => REQUIRED_STRING_VALIDATION,
            'email' => REQUIRED_EMAIL_VALIDATION,
            'phone' => REQUIRED_STRING_VALIDATION,
            'address' => NULLABLE_STRING_VALIDATION,
            'contacts_person_name' => ARRAY_VALIDATION,
            'contacts_person_name.*' => REQUIRED_STRING_VALIDATION,
            'contacts_email' => ARRAY_VALIDATION,
            'contacts_email.*' => REQUIRED_EMAIL_VALIDATION,
            'contacts_call_phone' => ARRAY_VALIDATION,
            'contacts_call_phone.*' => REQUIRED_STRING_VALIDATION,
            'contacts_phone' => ARRAY_VALIDATION,
            'contacts_phone.*' => REQUIRED_STRING_VALIDATION,
            'contacts_cities' => ARRAY_VALIDATION,
            'contacts_cities.*' => 'nullable',
            'contacts_cities.*.*' => [...REQUIRED_INTEGER_VALIDATION, ...['exists:cities,id']],
            'contacts_with' => ARRAY_VALIDATION,
            'contacts_with.*' => [...REQUIRED_STRING_VALIDATION, ...['In:individual,company,both']],
            'type' => [...NULLABLE_STRING_VALIDATION, 'In:factory,distributor,wholesales,retail'],
            'logo' => NULLABLE_IMAGE_VALIDATION,
            'cover' => NULLABLE_IMAGE_VALIDATION,
            'licenses' => ARRAY_VALIDATION,
            'licenses.*' => NULLABLE_IMAGE_VALIDATION,
            'latitude' => REQUIRED_STRING_VALIDATION,
            'longitude' => REQUIRED_STRING_VALIDATION,
            'facebook_url' => NULLABLE_URL_VALIDATION,
            'twitter_url' => NULLABLE_URL_VALIDATION,
            'youtube_url' => NULLABLE_URL_VALIDATION,
            'instagram_url' => NULLABLE_URL_VALIDATION,
            'pinterest_url' => NULLABLE_URL_VALIDATION,
            'website_url' => NULLABLE_URL_VALIDATION,
            'password' => NULLABLE_PASSWORD_VALIDATION
        ];
    }
}
