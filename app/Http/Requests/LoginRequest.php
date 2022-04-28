<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class LoginRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|min:8',
            'token_recapV3' => 'required|string',
        ];
    }

    public function messages(){
        return [
            'email.required' => '¡El email es obligatorio!',
            'email.email' => '¡El email debe tener formato de correo electronico!',
            'password.required' => '¡La contraena es obligatoria!',
            'password.min' => '¡La contrasena debe tener al menos 8 digitos!',
            'token_recapV3.required' => '¡El token es obligatorio!',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
