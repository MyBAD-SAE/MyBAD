<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperPublicView
 */
class PublicView extends Model
{
    protected $fillable = [
        'access_token',
        'school_class_id',
    ];

    protected $hidden = [
        'access_token',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
