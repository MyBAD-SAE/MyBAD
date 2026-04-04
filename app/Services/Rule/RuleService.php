<?php

namespace App\Services\Rule;

use App\Models\Rule;
use App\Models\HandicapParameter;

class RuleService
{
    public function getRuleForClass(int $classId): ?Rule
    {
        return Rule::with('handicapParameters')
            ->where('school_class_id', $classId)
            ->first();
    }

    public function updateRule(int $classId, array $data): Rule
    {
        $rule = Rule::updateOrCreate(
            ['school_class_id' => $classId],
            [
                'name' => 'Défis',
                'enable_ranking_groups' => $data['enable_ranking_groups'],
                'enable_elo_handicap' => $data['enable_elo_handicap'],
                'group_size' => $data['enable_ranking_groups'] ? ($data['group_size'] ?? null) : null,
            ]
        );

        if ($data['enable_elo_handicap'] && ! empty($data['handicap_parameters'])) {
            $rule->handicapParameters()->delete();

            foreach ($data['handicap_parameters'] as $param) {
                $rule->handicapParameters()->create([
                    'min_gap' => $param['min_gap'],
                    'max_gap' => $param['max_gap'],
                    'handicap' => $param['handicap'],
                ]);
            }
        } elseif (! $data['enable_elo_handicap']) {
            $rule->handicapParameters()->delete();
        }

        return $rule->load('handicapParameters');
    }
}