<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $commonRules = [
            'name_en' => REQUIRED_VALIDATION,
            'name_en.*' => NULLABLE_STRING_VALIDATION,
            'name_ar' => REQUIRED_VALIDATION,
            'name_ar.*' => NULLABLE_STRING_VALIDATION,
            'category_id' => REQUIRED_NUMERIC_VALIDATION,
            'short_desc_en' => NULLABLE_TEXT_VALIDATION,
            'short_desc_ar' => NULLABLE_TEXT_VALIDATION,
            'desc_en' => NULLABLE_TEXT_VALIDATION,
            'desc_ar' => NULLABLE_TEXT_VALIDATION,
            'type' => 'required|in:product,service',
            'cost' => 'required_if:type,product|numeric',
            'price' => 'required_if:type,product|numeric',
            'stock' => 'required_if:type,product|numeric',
            'unit_id' => 'integer|exists:units,id',
            'min_quantity' => NULLABLE_INTEGER_VALIDATION,
            'SKU' => NULLABLE_STRING_VALIDATION,

            'attributes' => ARRAY_VALIDATION,
            'attributes.*' => [...REQUIRED_INTEGER_VALIDATION, ...['exists:attribute_values,id']],

            'shipping_id' => [...NULLABLE_INTEGER_VALIDATION, ...['exists:shippings,id']],
            'specific_shipping' => NULLABLE_STRING_VALIDATION,
            'cars' => ARRAY_VALIDATION
        ];
        switch ($this->method()) {
            case 'POST':
                return array_merge($commonRules, [
                    'gallery' => REQUIRED_ARRAY_VALIDATION,
                    'gallery.*' => REQUIRED_IMAGE_VALIDATION,
                ]);
                break;
            case 'PUT':
                return array_merge($commonRules, [
                    'gallery' => ARRAY_VALIDATION,
                    'gallery.*' => NULLABLE_IMAGE_VALIDATION,
                ]);
                break;
        }
        return $commonRules;
    }
}
