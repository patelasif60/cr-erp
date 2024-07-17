<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MasterProductRequest extends FormRequest
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
            'product_temperature' => 'required',
            'product_type' => 'required',
            'pack_form_count' => 'required',
            'unit_in_pack' => 'required',
            'unit_description' => 'required',
            'brand' => 'required',
            'manufacturer' => 'sometimes|required',
            'weight' => 'required',
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
            // 'unit_weight' => 'sometimes|required',
            // 'unit_length' => 'sometimes|required',
            // 'unit_width' => 'sometimes|required',
            // 'unit_height' => 'sometimes|required',
            // 'unit_num' => 'sometimes|required',
            'item_form_description' => 'required',
            'full_product_desc' => 'required',
            'product_category' => 'required',
            'current_supplier' => 'required',
            'cost' => 'required',
            //'upc' => 'required|numeric',
            //'gtin' => 'required|numeric',
            // 'unit_upc' => 'numeric',
            // 'unit_gtin' => 'numeric',
            // 'country_of_origin' => 'sometimes|required',
            'warehouses_assigned' => 'required',
            'prop_65_ingredient' => 'required_if:prop_65_flag,Yes',
            'lobs' => 'required',
            // 'image.*' => 'required',
            // 'image_type.*' => 'required',
            // 'image_text.*' => 'required',
            "sup_type" => 'required'
        ];
    }
}
