<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperClassSession
 */
class ClassSession extends Model
{
    protected $fillable = [
        'school_class_id',
        'date',
        'is_active',
    ];

    protected $appends = ['session_name', 'formatted_date'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected function sessionName(): Attribute
    {
        return Attribute::get(fn () => 'Séance du ' . $this->date->format('d/m'));
    }

    protected function formattedDate(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->date->translatedFormat('l d F Y')));
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function gameMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'class_session_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForClass(Builder $query, int $classId): Builder
    {
        return $query->where('school_class_id', $classId);
    }
}
