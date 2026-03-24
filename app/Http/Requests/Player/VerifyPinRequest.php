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
            'pin'       => ['required', 'string', 'size:4'],
        ];
    }
}