<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:40',
            'last_name' => 'required|max:40',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'language_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            // 'first_name.required' => 'El campo nombre es obligatorio.',
            // 'first_name.max' => 'El campo nombre no puede superar los 40 caracteres.',
            // 'last_name.required' => 'El campo apellido es obligatorio.',
            // 'last_name.max' => 'El campo apellido no puede superar los 40 caracteres.',
            // 'email.required' => 'El campo email es obligatorio.',
            // 'email.email' => 'El email ingresado no es válido.',
            // 'email.unique' => 'El email ya está registrado en el sistema.',
            // 'email.max' => 'El email no puede superar los 255 caracteres.',
            // 'password.required' => 'El campo contraseña es obligatorio.',
            // 'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            // 'password_confirmation.required' => 'Debe confirmar la contraseña.',
            // 'password_confirmation.same' => 'La confirmación de la contraseña no coincide con la contraseña ingresada.',
            // 'language_id.required' => 'El campo ID de idioma es obligatorio.',
            'language_id.required' => 'The native language is required.',
        ];
    }
}
