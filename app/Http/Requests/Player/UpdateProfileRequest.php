<?php

namespace App\Http\Requests\Player;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = Auth::guard('player')->user();

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],

            'current_pin' => ['nullable', 'digits:4', 'required_with:new_pin'],
            'new_pin'     => ['nullable', 'digits:4', 'required_with:current_pin'],

            'current_password' => ['nullable', 'string', 'required_with:new_password', 'current_password:player'],
            'new_password'     => ['nullable', 'string', 'min:8', 'required_with:current_password', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.max'      => 'Le prénom ne peut pas dépasser 255 caractères.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'last_name.max'       => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required'      => 'L\'adresse email est obligatoire.',
            'email.email'         => 'L\'adresse email n\'est pas valide.',
            'email.unique'        => 'Cette adresse email est déjà utilisée.',

            'current_pin.digits'        => 'Le code PIN doit contenir exactement 4 chiffres.',
            'current_pin.required_with' => 'Le code PIN actuel est requis pour en définir un nouveau.',
            'new_pin.digits'            => 'Le nouveau code PIN doit contenir exactement 4 chiffres.',
            'new_pin.required_with'     => 'Le nouveau code PIN est requis.',

            'current_password.required_with' => 'Le mot de passe actuel est requis pour en définir un nouveau.',
            'new_password.min'               => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'new_password.confirmed'         => 'La confirmation du mot de passe ne correspond pas.',
            'new_password.required_with'     => 'Le nouveau mot de passe est requis.',
        ];
    }
}
