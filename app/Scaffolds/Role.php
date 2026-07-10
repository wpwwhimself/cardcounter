<?php

namespace App\Scaffolds;

use App\Scaffolds\Shipyard\Role as ShipyardRole;

class Role extends ShipyardRole
{
    protected static function items(): array
    {
        return [
            [
                "name" => "gamer",
                "icon" => "gamepad-variant",
                "description" => "Ma dostęp do statystyk zagranych gier.",
            ],
        ];
    }
}
