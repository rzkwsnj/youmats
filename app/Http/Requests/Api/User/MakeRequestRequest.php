<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\FormRequest;

class MakeRequestRequest extends FormRequest
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
            'pickup_latitude' => 'required',
            'pickup_longitude' => 'required',
            'destination_latitude' => 'required',
            'destination_longitude' => 'required',
            'car_type_id' => 'required|exists:car_types,id',
            'pickup_date' => 'required|date'
        ];
    }
}
