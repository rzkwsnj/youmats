<?php

namespace App\Http\Requests\Api\Driver;

use App\Http\Requests\Api\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'name' => REQUIRED_STRING_VALIDATION,
            'phone' => REQUIRED_STRING_VALIDATION,
            'phone2' => NULLABLE_STRING_VALIDATION,
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'driver_photo' => ARRAY_VALIDATION,
            'driver_photo.*' => NULLABLE_IMAGE_VALIDATION,
            'driver_id' => ARRAY_VALIDATION,
            'driver_id.*' => NULLABLE_IMAGE_VALIDATION,
            'driver_license' => ARRAY_VALIDATION,
            'driver_license.*' => NULLABLE_IMAGE_VALIDATION
        ];
    }
}
