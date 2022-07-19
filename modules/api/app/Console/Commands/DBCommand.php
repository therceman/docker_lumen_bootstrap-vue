<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DBCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): int
    {
        $dbRetryCounter = 0;
        $dbRetrySleep = 2;
        $dbConnectMaxRetry = 30;

        $this->info('ENV: '.env('ENV'));
        $this->info('MYSQL_PORT: '.env('MYSQL_PORT'));
        $this->info('DB_USER: '.env('DB_USER'));

        // wait until db is up
        while ($this->checkConnection() === false && $dbRetryCounter < $dbConnectMaxRetry) {
            $this->info('Waiting for database connection ... ' . ($dbRetrySleep * $dbRetryCounter) . 's');
            sleep($dbRetrySleep);
            $dbRetryCounter++;
        }

        if ($dbRetryCounter >= $dbConnectMaxRetry) {
            $this->error('Error. Database connection timed out.');
            return 1;
        }

        if ($this->hasBetsTable()) {
            $this->info('Database already migrated');
        } else {
            $res = Artisan::call('migrate', array('--path' => 'database/migrations', '--force' => true));
            $this->info($res === 0 ? 'Success. Database Migrated.' : 'Error! Database Migration Failed');
        }

        return 0;
    }

    public function hasBetsTable(): bool
    {
        try {
            DB::select('select * from client');
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function checkConnection(): bool
    {
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}