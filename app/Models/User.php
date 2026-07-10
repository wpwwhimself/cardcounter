<?php

namespace App\Models;

use App\Models\Shipyard\User as ShipyardUser;

class User extends ShipyardUser
{
    public const FROM_SHIPYARD = true;

    protected $fillable = [
        "name",
        "email",
        "password",
        "roles",
        "game_stats",
    ];

    #region attributes
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            "game_stats" => "array",
        ];
    }
    #endregion
}
