<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropIngredientsRequest extends FormRequest
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
            'prop_ingredients' => 'required|unique:prop_ingredients,prop_ingredients,'.$this->id,

        ];
    }
}
