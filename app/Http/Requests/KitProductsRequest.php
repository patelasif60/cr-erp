<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KitProductsRequest extends FormRequest
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
            'ETIN' => 'required',
            'status' => 'required',
            'etailer_availability' => 'required',
            'product_type' => 'required',
            'brand' => 'required',
            'full_product_desc' => 'required',
            'product_category' => 'required',
            'cost' => 'required',
            'lobs' => 'required',
            'product_temperature' => 'required',
            'warehouses_assigned' => 'required',
            // 'upc' => 'required',
            // 'gtin' => 'required',
        ];
    }
}
