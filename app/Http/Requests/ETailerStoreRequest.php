<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ETailerStoreRequest extends FormRequest
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
            'etailer_availability' =>'required|unique:etailer_availability,etailer_availability,'.$this->id,
        ];
    }
}
