<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enable_ranking_groups' => ['required', 'boolean'],
            'enable_elo_handicap' => ['required', 'boolean'],
            'group_size' => ['nullable', 'integer', 'min:2'],
            'handicap_parameters' => ['nullable', 'array'],
            'handicap_parameters.*.min_gap' => ['required_with:handicap_parameters', 'integer', 'min:0'],
            'handicap_parameters.*.max_gap' => ['required_with:handicap_parameters', 'integer', 'min:0'],
            'handicap_parameters.*.handicap' => ['required_with:handicap_parameters', 'integer'],
        ];
    }
}