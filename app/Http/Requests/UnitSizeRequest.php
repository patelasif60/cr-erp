<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitSizeRequest extends FormRequest
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
            'unit' => ['required', 'string', 'max:255'],
            'unit' => 'required|unique:unit_sizes,unit,'.$this->id,
            'abbreviation' => ['required', 'max:255'],
        ];
    }
}
