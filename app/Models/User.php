<?php

namespace App\Models;

use App\Models\Shipyard\User as ShipyardUser;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    public function profileComponents(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                view("components.game-stats.user-stats", [
                    "user" => $this,
                ])->render(),
            ],
        );
    }
}
