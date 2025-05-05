<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear log files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path(env('LOG_FILE', 'logs/laravel.log'));

        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
            $this->info('Log file cleared.');
        } else {
            $this->warn('Log file does not exist.');
        }
    }
}
