<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountryOfOriginRequest extends FormRequest
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
            'country_of_origin' => 'required|unique:country_of_origin,country_of_origin,'.$this->id,
        ];
    }
}
