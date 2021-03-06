<?php

namespace Redbill;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Redbill\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'name', 'email', 'password',
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden
        = [
            'password', 'remember_token',
        ];
}
