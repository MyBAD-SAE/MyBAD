<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rule extends Model
{
    protected $fillable = [
        'name',
        'enable_ranking_groups',
        'enable_elo_handicap',
        'group_size',
        'school_class_id',
    ];

    protected function casts(): array
    {
        return [
            'enable_ranking_groups' => 'boolean',
            'enable_elo_handicap' => 'boolean',
            'group_size' => 'integer',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function handicapParameters(): HasMany
    {
        return $this->hasMany(HandicapParameter::class, 'rule_id');
    }
}