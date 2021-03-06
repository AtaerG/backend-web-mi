<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AppointmentRequest extends FormRequest
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
            'user_id' => 'required|numeric',
            'admin_id' => 'required|numeric',
            'date'=>'required|regex:/^\d{2}\/\d{2}\/\d{4}$/',
            'time'=>'required|regex:/^\d{2}:\d{2}$/',
        ];
    }

    public function messages(){
        return [
            'date.required' => '¡La fecha es obligatoria!',
            'time.required' =>  '¡El tiempo es obligatorio!',
            'date.regex' => '¡La fecha debe tener el formato de fecha!',
            'time.regex' => '¡El tiempo debe tener el formato de HH:SS!',
            'user_id.required' => '¡El id de usuario es obligatorio!',
            'admin_id.required' => '¡El id de administrador es obligatorio!',
            'user_id.numeric' => '¡El id de usuario debe ser un numero!',
            'admin_id.numeric' => '¡El id de administrador debe ser un numero!'
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));

}
}
