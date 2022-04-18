<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ProductRequest extends FormRequest
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
        'name'=>'required',
        'amount'=>'required|numeric|gt:0',
        'price'=>'required|numeric|gt:0',
        ];
    }

    public function messages(){
        return [
            'name.required' => 'El nombre es obligatorio!',
            'price.required' =>  'El precio es obligatorio!',
            'amount.min' => 'La cantidad debe ser mayor o igual que 0!',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'status' => true
        ], 422));

}
}
