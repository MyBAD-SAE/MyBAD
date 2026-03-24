<?php

namespace App\Http\Requests\Player;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'opponent_id'    => ['required', 'exists:players,id'],
            'my_score'       => ['required', 'integer', 'min:0', 'max:21'],
            'opponent_score' => ['required', 'integer', 'min:0', 'max:21'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->my_score < 15 && $this->opponent_score < 15) {
                $validator->errors()->add(
                    'my_score',
                    'Au moins un des deux scores doit être de 15 points minimum.'
                );
            }
        });
    }
}