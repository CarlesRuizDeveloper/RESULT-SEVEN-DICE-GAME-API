<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'nullable|unique:users',
            'email' => 'required|min:3',
            'password' => 'required|min:8|regex:/^[a-zA-Z0-9]+$/',
        ];
    }

    public function messages()
    {
        return [
            'name.unique'=>'Este nombre ya esta en uso.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.min' => 'El correo electrónico no es válido',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña solo puede contener letras mayúsculas, minúsculas o números.',
        ];
    }
}