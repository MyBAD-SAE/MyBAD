<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin IdeHelperSchoolClass
 */
class SchoolClass extends Model
{
    protected $fillable = [
        'school_year',
        'name',
        'address',
        'description',
    ];

    public function algorithmParameters(): HasMany
    {
        return $this->hasMany(AlgorithmParameter::class, 'school_class_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ClassParticipant::class, 'school_class_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'school_class_id');
    }

    public function publicView(): HasOne
    {
        return $this->hasOne(PublicView::class, 'school_class_id');
    }

    public function rule(): HasOne
    {
        return $this->hasOne(Rule::class, 'school_class_id');
    }
}
