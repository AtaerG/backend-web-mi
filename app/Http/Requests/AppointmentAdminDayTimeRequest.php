<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AppointmentAdminDayTimeRequest extends FormRequest
{
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
            'admin_id' => 'required|gt:0',
            'date'=>'required|regex:/^\d{2}\/\d{2}\/\d{4}$/',
            'time'=>'required|regex:/^\d{2}:\d{2}$/',
        ];
    }

    public function messages(){
        return [
            'admin_id.required' => '¡El id de administrador es obligatorio!',
            'admin_id.gt' => '¡El id de administrador debe ser un numero mayor que 0!',
            'date.required' => '¡La fecha es obligatoria!',
            'time.required' =>  '¡El tiempo es obligatorio!',
            'date.regex' => '¡La fecha debe tener el formato de fecha!',
            'time.regex' => '¡El tiempo debe tener el formato de HH:SS!',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
