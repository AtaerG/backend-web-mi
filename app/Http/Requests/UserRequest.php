<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UserRequest extends FormRequest
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
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email',
            'role' => 'required',
        ];
    }

    public function messages(){
        return [
            'name.required' => '¡El nombre es obligatorio!',
            'surname.required' =>  '¡Los apellidos son obligatorias!',
            'email.required' =>  '¡Correo electronico es obligatorio!',
            'email.email' =>  '¡Correo electronico debe tener formato de correo electronico!',
            'role.required' => '¡Rolo de usuairo es obligatorio!',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
