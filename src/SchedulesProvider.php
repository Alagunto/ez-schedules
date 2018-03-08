<?php

namespace Alagunto\EzSchedules;

class SchedulesProvider extends \Illuminate\Support\ServiceProvider
{
    public function register() {

    }

    public function boot() {
        $this->loadMigrationsFrom(__DIR__ . "/Database/Migrations");

        $this->provides([
            "schedules" => SchedulesManager::class
        ]);
    }
}