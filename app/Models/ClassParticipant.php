<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperClassParticipant
 */
class ClassParticipant extends Model
{
    protected $fillable = [
        'participantable_type',
        'participantable_id',
        'elo_rating',
        'school_class_id',
    ];

    protected function casts(): array
    {
        return [
            'elo_rating' => 'decimal:2',
        ];
    }

    public function participantable(): MorphTo
    {
        return $this->morphTo();
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
