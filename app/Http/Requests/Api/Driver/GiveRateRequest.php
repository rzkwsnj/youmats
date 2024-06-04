<?php

namespace App\Http\Requests\Api\Driver;

use App\Http\Requests\Api\FormRequest;

class GiveRateRequest extends FormRequest
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
            'rate' => 'required|integer|between:1,5',
            'review' => NULLABLE_TEXT_VALIDATION
        ];
    }
}
