<?php

namespace App\Support;

class MigrationEnvironment
{
    public static function isDesktopInstall(): bool
    {
        return filter_var(getenv('MININGERP_DESKTOP') ?: ($_ENV['MININGERP_DESKTOP'] ?? false), FILTER_VALIDATE_BOOL);
    }
}
