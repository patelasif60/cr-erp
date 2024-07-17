<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class BrandStoreRequest extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            // 'brand' => 'required|unique:brand,brand,'.$this->id,
            'manufacturer_id' => "required",
            'brand' => ['required', Rule::unique('brand') ->where('manufacturer_id', $this->manufacturer_id) ]
        ];
    }
}
