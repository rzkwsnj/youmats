<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
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
            'city_id' => [...REQUIRED_INTEGER_VALIDATION, ...['exists:cities,id']],
            'phone_number' => REQUIRED_STRING_VALIDATION,
            'fax' => NULLABLE_STRING_VALIDATION,
            'website' => NULLABLE_STRING_VALIDATION,
            'address' => REQUIRED_STRING_VALIDATION,
            'latitude' => REQUIRED_STRING_VALIDATION,
            'longitude' => REQUIRED_STRING_VALIDATION
        ];
    }
}
