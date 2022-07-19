<?php

namespace App\Console;

use App\Console\Commands\DBCommand;
use App\Console\Commands\EnvCommand;
use App\Console\Commands\MigrationCommand;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        EnvCommand::class,
        DBCommand::class,
        MigrationCommand::class
    ];
}
