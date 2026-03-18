<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'password',
        'profile_picture',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function player(): HasOne
    {
        return $this->hasOne(Player::class);
    }

    public function adminUser(): HasOne
    {
        return $this->hasOne(AdminUser::class);
    }

    public function setLastNameAttribute(?string $value): void
    {
        $this->attributes['last_name'] = $value === null ? null : Str::upper($value);
    }

    public function setFirstNameAttribute(?string $value): void
    {
        $this->attributes['first_name'] = $value === null ? null : Str::ucfirst(Str::lower($value));
    }

    public function setEmailAttribute(string $value): void
    {
        $this->attributes['email'] = Str::lower($value);
    }


    // public function sendPasswordResetNotification($token): void
    // {
    //     $route = Request::route();

    //     $domain = null;
    //     if ($route instanceof Route && isset($route->action['domain'])) {
    //         $domain = $route->action['domain'];
    //     }

    //     $notification = new SendResetLinkToUser($token, $domain);
    //     $this->notify($notification);
    // }


    public function getFullNameAttribute(): ?string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
