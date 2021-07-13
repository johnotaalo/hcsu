<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MakeModelRequest extends FormRequest
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
            'MAKE_MODEL'    =>  'required|unique:2019.ref_make_model,MAKE_MODEL'
        ];
    }

    public function messages(){
        return [
            'MAKE_MODEL.required'   =>  'The Make & Model is required',
            'MAKE_MODEL.unique'     =>  'The Make & Model entered already exists'
        ];
    }
}
