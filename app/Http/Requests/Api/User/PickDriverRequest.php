<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\FormRequest;

class PickDriverRequest extends FormRequest
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
            'trip_id' => 'required|integer|exists:trips,id',
            'driver_id' => 'required|integer|exists:drivers,id'
        ];
    }
}
