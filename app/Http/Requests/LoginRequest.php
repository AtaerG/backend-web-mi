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
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8'
        ];
    }

    public function messages(){
        return [
            'email.required' => '¡El email es obligatorio!',
            'email.email' => '¡El email debe tener formato de correo electronico!',
            'email.exists' => '¡El email debe existir!',
            'password.required' => '¡La contraena es obligatoria!',
            'password.min' => '¡La contraena debe tener al menos 8 digitos!',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
