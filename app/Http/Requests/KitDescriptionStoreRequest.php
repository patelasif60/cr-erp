<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KitDescriptionStoreRequest extends FormRequest
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
            'kit_description' => 'required|unique:kit_description,kit_description,'.$this->id,
        ]; 
    }
}
