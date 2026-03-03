<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'challenged_id' => [
                'required',
                'exists:users,id',
                Rule::notIn([$this->user()->id]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'challenged_id.not_in' => 'You cannot challenge yourself.',
        ];
    }
}
