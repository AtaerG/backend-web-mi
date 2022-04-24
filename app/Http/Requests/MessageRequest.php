<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class MessageRequest extends FormRequest
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
            'id'=>'required|gt:0',
            'name'=>'required',
            'message'=>'required',
        ];
    }

    public function messages(){
        return [
            'id.required' => '¡El id de session de mensajes es obligatorio!',
            'id.gt' => '¡El id debe ser mayr que 0!',
            'name.required' => '¡Nombre de emitente es obligatorio!',
            'message.required' => '¡Mensaje es obligatorio!',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
