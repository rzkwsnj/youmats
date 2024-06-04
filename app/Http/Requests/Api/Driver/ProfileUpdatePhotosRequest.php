<?php

namespace App\Http\Requests\Api\Driver;

use App\Http\Requests\Api\FormRequest;

class ProfileUpdatePhotosRequest extends FormRequest
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
            'driver_photo' => ARRAY_VALIDATION,
            'driver_photo.*' => NULLABLE_IMAGE_VALIDATION,
            'driver_id' => ARRAY_VALIDATION,
            'driver_id.*' => NULLABLE_IMAGE_VALIDATION,
            'driver_license' => ARRAY_VALIDATION,
            'driver_license.*' => NULLABLE_IMAGE_VALIDATION
        ];
    }
}
