<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
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
            'cover' => NULLABLE_IMAGE_VALIDATION,
            'profile' => NULLABLE_IMAGE_VALIDATION,
            'licenses' => ARRAY_VALIDATION,
            'licenses.*' => NULLABLE_IMAGE_VALIDATION,
            'name' => REQUIRED_STRING_VALIDATION,
//            'email' => 'required|max:191|email|unique:users,email,' . auth()->user()->id,
            'phone' => REQUIRED_STRING_VALIDATION,
            'phone2' => NULLABLE_STRING_VALIDATION,
            'address' => REQUIRED_STRING_VALIDATION,
            'address2' => NULLABLE_STRING_VALIDATION,
            'latitude' => NULLABLE_STRING_VALIDATION,
            'longitude' => NULLABLE_STRING_VALIDATION,
            'password' => NULLABLE_PASSWORD_VALIDATION,
        ];
    }
}
