<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuppliersRequest extends FormRequest
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
            'name' => 'required',
            'address' => 'required',
            'address2' => 'required',
            'main_point_of_contact' => 'required',
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|email|unique:suppliers,email,'.$this->id,
            'csv_formate' => 'required|sometimes',
            // 'description' => 'required',
            'supplier_product_package_type' => 'required',
            'warehouses_assigned' => 'required'
        ];
    }
}
