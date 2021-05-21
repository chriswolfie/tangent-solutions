<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;

trait MigrateOnce
{
    private static $migration_has_run = false;

    protected function freshMigration(): void
    {
        echo "CaLlInG!!!\n";
        if (self::$migration_has_run === false) {
            echo "MiGrAtInG!!!\n";
            Artisan::call('migrate:fresh');
            self::$migration_has_run = true;
        }
    }
}