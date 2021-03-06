<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class OrderUpdateRequest extends FormRequest
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
            'direction'=>'required',
            'post_code'=>'required|numeric',
            'city'=>'required',
            'state'=>'required',
            'country'=>'required',
        ];
    }

    public function messages(){
        return [
            'direction' => '¡La direccion es obligatoria!',
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
