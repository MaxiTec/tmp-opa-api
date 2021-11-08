<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyPostRequest extends FormRequest
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
            'name'=> 'required|string',
            'manager' => 'required|string|max:100',
            'code' => 'required|unique:properties,code,{$this->post->id}',
            'brand_img' => 'required|image|max:1024',
            'address' => 'required|string',
            'phone' => 'required|string',
            'lat' => 'required|string',
            'lon' => 'required|string',
            'phone_code' => 'required|string',
            'rooms' => 'required|string'
        ];
    }
}