<?php

namespace App\Http\Requests\Api\Driver;

use App\Http\Requests\Api\FormRequest;

class RequestResponseRequest extends FormRequest
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
            'response' => 'required|integer|In:1,2'
        ];
    }
}
