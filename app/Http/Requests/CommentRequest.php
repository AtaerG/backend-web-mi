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
            'stars'=>'required|numeric|gte:0|lte:5',
            'user_id'=>'required|numeric',
            'product_id'=>'required|numeric'
        ];
    }

    public function messages(){
        return [
            'content.required' => 'El contenido es obligatorio!',
            'stars.required' => 'Cantidad de estrellas es obligatorio!',
            'stars.gte' => 'La cantidad debe ser mayor o igual 0',
            'stars.lte' => 'La cantidad debe ser menor o igual que 5'
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'status' => true
        ], 422));
    }
}
