<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admins';

    protected $fillable = [
        'email',
        'name',
    ];

    protected $hidden = [
        'remember_token',
    ];

    /**
     * 根据用户名查找管理员
     */
    public static function findByUsername(string $username): ?self
    {
        return static::where('name', $username)->first();
    }
}
