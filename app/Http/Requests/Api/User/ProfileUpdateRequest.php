<?php

namespace App\Http\Requests\Api\User;

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
            'profile' => NULLABLE_IMAGE_VALIDATION,
            'name' => REQUIRED_STRING_VALIDATION,
            'phone' => REQUIRED_STRING_VALIDATION,
            'phone2' => NULLABLE_STRING_VALIDATION,
            'address' => REQUIRED_STRING_VALIDATION,
            'address2' => NULLABLE_STRING_VALIDATION,
            'password' => NULLABLE_PASSWORD_VALIDATION
        ];
    }
}
