<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class CancelSubscribe extends FormRequest
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
            'membership_id' => [...REQUIRED_INTEGER_VALIDATION, ...['exists:memberships,id']],
            'category_id' => [...REQUIRED_INTEGER_VALIDATION, ...['exists:categories,id']]
        ];
    }
}
