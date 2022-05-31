<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class OrderValorationRequest extends FormRequest
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
            'valoration'=>'required|gte:0|lte:5',
        ];
    }

    public function messages(){
        return [
            'valoration.required' => '¡La valoracion del pedido es obligatorio!',
            'valoration.gte' => '¡La valoracion del pedido debe ser mayor o igual que 0!',
            'valoration.lte' => '¡La valoracion del pedido debe ser menor o igual que 5!',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
