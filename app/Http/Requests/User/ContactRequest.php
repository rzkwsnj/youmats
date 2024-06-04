<?php

namespace App\Http\Requests\User;

use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'name' => REQUIRED_STRING_VALIDATION,
            'email' => REQUIRED_EMAIL_VALIDATION,
            'phone' => ['required', new PhoneNumberRule()],
            'message' => NULLABLE_TEXT_VALIDATION
        ];
    }
}
