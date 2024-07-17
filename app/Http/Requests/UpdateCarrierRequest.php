<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarrierRequest extends FormRequest
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
            'company_name' => 'required',
            'main_point_of_contact' => 'required',
            'client_address' => 'required',
            'client_address2' => 'required',
            'client_phone' => 'required',
            'client_email' => 'required|email|unique:carriers,client_email,'.$this->id,
        ];
    }
}
