<?php

namespace App\Http\Requests\User;

use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;

class InquireRequest extends FormRequest
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
            'company_name' => REQUIRED_STRING_VALIDATION,
            'name' => REQUIRED_STRING_VALIDATION,
            'email' => REQUIRED_EMAIL_VALIDATION,
            'quotation_phone' => ['required', new PhoneNumberRule()],
            'message' => NULLABLE_TEXT_VALIDATION,
            'file' => NULLABLE_FILE_VALIDATION
        ];
    }
}