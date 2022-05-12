<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CommentRequest extends FormRequest
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
            'content'=>'required',
            'valoration'=>'required|gte:0|lte:5',
            'user_id'=>'required|numeric',
            'product_id'=>'required|numeric',
            'order_id'=>'required|numeric',
        ];
    }

    public function messages(){
        return [
            'content.required' => '¡El contenido es obligatorio!',
            'valoration.required' => '¡Cantidad de estrellas es obligatorio!',
            'user_id.required' => '¡El id de usuario es obligatorio!',
            'product_id.required' => '¡El id de producto es obligatorio!',
            'user_id.numeric' => '¡El id de usuario debe ser un numero!',
            'product_id.numeric' => '¡El id de producto debe ser un numero!',
            'order_id.numeric' => '¡El id de pedido debe ser un numero!',
            'order_id.numeric' => '¡El id de pedido debe ser un numero!',
            'valoration.gte' => '¡La cantidad debe ser mayor o igual 0!',
            'valoration.lte' => 'La cantidad debe ser menor o igual que 5',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
