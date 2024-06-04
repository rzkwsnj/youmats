<?php

namespace App\Http\Requests\User;

use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
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
            'payment_method' => [...[NULLABLE_STRING_VALIDATION], 'In:Cash,Online', Rule::requiredIf(fn() => is_individual())],
            'terms' => 'required|accepted',
            'name' => REQUIRED_STRING_VALIDATION,
            'phone_number' => NULLABLE_INTEGER_VALIDATION,
            'address' => REQUIRED_STRING_VALIDATION,
            'building_number' => NULLABLE_INTEGER_VALIDATION,
            'street' => NULLABLE_STRING_VALIDATION,
            'district' => NULLABLE_STRING_VALIDATION,
            'city' => NULLABLE_INTEGER_VALIDATION,
            'email' => REQUIRED_EMAIL_VALIDATION,
            'notes' => NULLABLE_STRING_VALIDATION,
            'notes.*.title' => NULLABLE_STRING_VALIDATION,
            'delivery_time' => [...[NULLABLE_STRING_VALIDATION], Rule::requiredIf(fn() => is_company())],
            'delivery_time_unit' => [...[NULLABLE_STRING_VALIDATION], Rule::requiredIf(fn() => is_company()), 'In:day,week,month'],
            'attachments.*' => NULLABLE_FILE_VALIDATION,
            'latitude' => NULLABLE_STRING_VALIDATION,
            'longitude' => NULLABLE_STRING_VALIDATION
        ];
    }
}
