<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageTypeStoreRequest extends FormRequest
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
            'image_type' => 'required|unique:image_type,image_type,'.$this->id,
        ];
    }
}
