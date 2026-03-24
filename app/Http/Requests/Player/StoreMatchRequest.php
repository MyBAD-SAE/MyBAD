<?php

namespace App\Http\Requests\Player;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'opponent_id' => ['required', 'exists:players,id'],
            'my_score' => ['required', 'integer', 'min:0', 'max:99'],
            'opponent_score' => ['required', 'integer', 'min:0', 'max:99'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $myScore = (int) $this->my_score;
            $opponentScore = (int) $this->opponent_score;

            if ($myScore < 15 && $opponentScore < 15) {
                $validator->errors()->add(
                    'my_score',
                    'Au moins un des deux scores doit être de 15 points minimum.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'opponent_id.required' => 'L\'adversaire est obligatoire.',
            'opponent_id.exists' => 'Cet adversaire n\'existe pas.',
            'my_score.required' => 'Votre score est obligatoire.',
            'my_score.integer' => 'Le score doit être un nombre entier.',
            'my_score.min' => 'Le score ne peut pas être négatif.',
            'my_score.max' => 'Le score ne peut pas dépasser 99.',
            'opponent_score.required' => 'Le score de l\'adversaire est obligatoire.',
            'opponent_score.integer' => 'Le score doit être un nombre entier.',
            'opponent_score.min' => 'Le score ne peut pas être négatif.',
            'opponent_score.max' => 'Le score ne peut pas dépasser 99.',
        ];
    }
}