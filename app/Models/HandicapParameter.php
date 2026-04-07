<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HandicapParameter extends Model
{
    protected $fillable = [
        'min_gap',
        'max_gap',
        'handicap',
        'rule_id',
    ];

    protected function casts(): array
    {
        return [
            'min_gap' => 'integer',
            'max_gap' => 'integer',
            'handicap' => 'integer',
        ];
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'rule_id');
    }
}