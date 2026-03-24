<?php

namespace App\Http\Requests\Player;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'player_id' => ['required', 'exists:players,id'],
            'pin' => ['required', 'string', 'size:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'player_id.required' => 'L\'identifiant du joueur est obligatoire.',
            'player_id.exists' => 'Ce joueur n\'existe pas.',
            'pin.required' => 'Le code PIN est obligatoire.',
            'pin.size' => 'Le code PIN doit contenir 4 caractères.',
        ];
    }
}