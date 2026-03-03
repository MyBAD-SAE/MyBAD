<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMatchScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $match = $this->route('match');
        return $match->status === 'accepted'
            && $match->involvesUser($this->user());
    }

    public function rules(): array
    {
        return [
            'challenger_score_set1' => ['required', 'integer', 'min:0', 'max:30'],
            'challenged_score_set1' => ['required', 'integer', 'min:0', 'max:30'],
            'challenger_score_set2' => ['required', 'integer', 'min:0', 'max:30'],
            'challenged_score_set2' => ['required', 'integer', 'min:0', 'max:30'],
            'challenger_score_set3' => ['nullable', 'integer', 'min:0', 'max:30'],
            'challenged_score_set3' => ['nullable', 'integer', 'min:0', 'max:30'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $this->all();
            // Validate each set has a valid badminton score
            $this->validateSet($validator, $data, 1);
            $this->validateSet($validator, $data, 2);

            // Check if set 3 is needed
            $set1Winner = $this->setWinner($data, 1);
            $set2Winner = $this->setWinner($data, 2);

            if ($set1Winner && $set2Winner && $set1Winner !== $set2Winner) {
                // Need set 3
                if (empty($data['challenger_score_set3']) && empty($data['challenged_score_set3'])) {
                    $validator->errors()->add('challenger_score_set3', 'Set 3 is required when sets are tied 1-1.');
                } else {
                    $this->validateSet($validator, $data, 3);
                }
            } elseif ($set1Winner && $set2Winner && $set1Winner === $set2Winner) {
                // 2-0, set 3 should not be present
                if (!empty($data['challenger_score_set3']) || !empty($data['challenged_score_set3'])) {
                    $validator->errors()->add('challenger_score_set3', 'Set 3 is not needed when one player won 2-0.');
                }
            }
        });
    }

    private function validateSet($validator, array $data, int $set): void
    {
        $cs = $data["challenger_score_set{$set}"] ?? null;
        $ds = $data["challenged_score_set{$set}"] ?? null;

        if ($cs === null || $ds === null) return;

        $high = max($cs, $ds);
        $low = min($cs, $ds);

        if ($high < 21) {
            $validator->errors()->add("challenger_score_set{$set}", "Set {$set}: the winning score must be at least 21.");
        } elseif ($high === 21 && $low > 19) {
            $validator->errors()->add("challenger_score_set{$set}", "Set {$set}: with a score of 21, the other player cannot have more than 19.");
        } elseif ($high > 21 && $high - $low !== 2) {
            $validator->errors()->add("challenger_score_set{$set}", "Set {$set}: above 21, the difference must be exactly 2.");
        } elseif ($high > 30) {
            $validator->errors()->add("challenger_score_set{$set}", "Set {$set}: the maximum score is 30-29.");
        }

        if ($cs === $ds) {
            $validator->errors()->add("challenger_score_set{$set}", "Set {$set}: scores cannot be equal.");
        }
    }

    private function setWinner(array $data, int $set): ?string
    {
        $cs = $data["challenger_score_set{$set}"] ?? null;
        $ds = $data["challenged_score_set{$set}"] ?? null;

        if ($cs === null || $ds === null) return null;

        return $cs > $ds ? 'challenger' : 'challenged';
    }
}
