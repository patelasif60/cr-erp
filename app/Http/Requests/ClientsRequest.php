<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientsRequest extends FormRequest
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
            // 'client_or_channel' => 'required',
            // 'address' => 'required',
            // 'phone' => 'required',
            // 'email' => 'required|unique:suppliers,email,'.$this->id,
            'company_name' => 'required',
            // 'main_point_of_contact' => 'required',
        ];
    }
}
