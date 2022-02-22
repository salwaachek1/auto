<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarStoreRequest extends FormRequest
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
            'model' => 'required',
            'serial_number' => 'required',
            'place' => 'required',
            'kilo' => 'required',
            'images' => 'image|mimes:jpg,jpeg,png,gif,svg|max:2048'
        ];
    }

     /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'model.required' => 'veuillez saisir le nom de modèle !',
            'serial_number.required' => 'veuillez saisir la matricule !',
            'place.required' => 'veuillez saisir le lieu !',
            'kilo.required' => 'veuillez saisir le kilométrage !',
            'images.image' => 'veuillez saisir un format valide d\'image !',
        ];
    }
}
