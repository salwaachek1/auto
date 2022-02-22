<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'email' => 'required',
            'name' => 'required',
            'password' => 'required',
            'images' => 'image|mimes:jpg,jpeg,png,gif,svg|max:2048',
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
            'email.required' => 'veuillez saisir l\'email de modèle !',
            'name.required' => 'veuillez saisir le nom !',
            'password.required' => 'veuillez saisir le mot de passe !',
            'images.image' => 'veuillez saisir un format valide d\'image !',
        ];
    }
}
