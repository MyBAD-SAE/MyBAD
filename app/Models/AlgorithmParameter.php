<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperAlgorithmParameter
 */
class AlgorithmParameter extends Model
{
    protected $fillable = [
        'min_diff',
        'max_diff',
        'winner_points',
        'school_class_id',
    ];

    protected function casts(): array
    {
        return [
            'min_diff' => 'integer',
            'max_diff' => 'integer',
            'winner_points' => 'decimal:2',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
