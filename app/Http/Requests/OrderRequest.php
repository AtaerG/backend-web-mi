<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class OrderRequest extends FormRequest
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

    public function rules()
    {
        return [
            'products'=>'required',
            'total_price'=>'required|gt:0',
            'status'=>'required|in:pagado,terminado',
            'direction'=>'required',
            'post_code'=>'required|numeric',
            'city'=>'required',
            'state'=>'required',
            'country'=>'required',
        ];
    }

    public function messages(){
        return [
            'products'=>'¡Los productos del pedido son obligatorios!',
            'total_price.required' => '¡El precio total del pedido es obligatorio!',
            'status.required' => '¡El estado del pedido es obligatorio!',
            'status.in' => '¡El estado del pedido debe ser uno de los siguientes: pagado, terminado!',
            'direction' => '¡La direccion es obligatoria!',
            'total_price.gt' => '¡El precio total debe ser mayor que 0!',
            'post_code.required' => '¡El codigo postal es obligatorio!',
            'post_code.numeric' => '¡El codigo postal debe ser numerico!',
            'city.required' => '¡La ciudad es obligatoria!',
            'state.required' => '¡La provincia es obligatoria!',
            'country.required' => '¡El pais es obligatorio!',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
