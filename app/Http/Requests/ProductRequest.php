<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ProductRequest extends FormRequest
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
        'name'=>'required',
        'amount'=>'required|gte:0',
        'price'=>'required|gt:0',
        'description'=>'required',
        'image_url'=>'required',
        'tag'=>'required'
        ];
    }

    public function messages(){
        return [
            'name.required' => '¡El nombre es obligatorio!',
            'amount.required' =>  '¡La cantidad es obligatoria!',
            'price.required' =>  '¡El precio es obligatorio!',
            'amount.gte' => '¡La cantidad debe ser mayor o igual que 0!',
            'price.gt' => '¡El precio debe ser mayor que 0!',
            'description.required' => '¡La descripcion es obligatoria!',
            'image_url.required' => '¡La imagen es obligatoria!',
            'tag.required' => '¡El tag es obligatorio!'
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
