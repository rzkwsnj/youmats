<?php

namespace App\Http\Requests\Api\Driver;

use App\Http\Requests\Api\FormRequest;

class CarRequest extends FormRequest
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
        switch ($this->method()) {
            case 'POST' : // Store
            case 'PUT': // Update
                return [
                    'type_id' => 'required|integer|exists:car_types,id',
                    'name_ar' => NULLABLE_STRING_VALIDATION,
                    'name_en' => NULLABLE_STRING_VALIDATION,
                    'model' => REQUIRED_STRING_VALIDATION,
                    'license_no' => REQUIRED_STRING_VALIDATION,
                    'max_load' => REQUIRED_INTEGER_VALIDATION,
                    'price_per_kilo' => REQUIRED_NUMERIC_VALIDATION,

                    'car_photo' => ARRAY_VALIDATION,
                    'car_photo.*' => NULLABLE_IMAGE_VALIDATION,
                    'car_license' => ARRAY_VALIDATION,
                    'car_license.*' => NULLABLE_IMAGE_VALIDATION
                ];
                break;
        }
    }
}
