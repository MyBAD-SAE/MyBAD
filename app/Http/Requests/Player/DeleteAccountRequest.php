<?php

namespace App\Http\Requests\Player;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'confirmation' => ['required', 'in:SUPPRIMER'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirmation.in' => 'Vous devez taper SUPPRIMER pour confirmer la suppression.',
        ];
    }
}
