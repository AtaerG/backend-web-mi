<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PasswordResetedRequest extends FormRequest
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
            'token' => 'required',
            'password' => 'required|min:8',
            'password_confirm' => 'required|same:password|min:8',
        ];
    }

    public function messages(){
        return [
            'token.required' => '¡Error. No se puede cambiar la contraseña. Intenta mas tarde!',
            'password.required' =>  '¡Error. No se puede cambiar la contraseña. Intenta mas tarde!',
            'password_confirm.required' =>  '¡Error. No se puede cambiar la contraseña. Intenta mas tarde!',
            'password_confirm.same' =>  '¡Error. No se puede cambiar la contraseña. Intenta mas tarde!',
            'password.min' =>  '¡Error. No se puede cambiar la contraseña. Intenta mas tarde!',
            'password_confirm.min' =>  '¡Error. No se puede cambiar la contraseña. Intenta mas tarde!',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));

    }
}
