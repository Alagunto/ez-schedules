<?php

namespace Alagunto\EzSchedules;

class SchedulesProvider extends \Illuminate\Support\ServiceProvider
{
    public function register() {

    }

    public function boot() {
//        $this->loadMigrationsFrom(__DIR__ . "/Database/Migrations");

        $this->provides([
            "schedules" => SchedulesManager::class
        ]);

        $this->publishes([
            __DIR__ . "/Database/Migrations/2010_ez_schedules_create_repetition_rules_table.php"
            => base_path("database/migrations/2010_ez_schedules_create_repetition_rules_table.php")
        ]);
    }
}