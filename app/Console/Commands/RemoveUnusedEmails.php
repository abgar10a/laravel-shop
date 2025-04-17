<?php

namespace App\Console\Commands;

use App\Models\Email;
use Illuminate\Console\Command;

class RemoveUnusedEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove sent emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Email::where('sent', 1)
            ->orderBy('id')
            ->cursor()
            ->each(function (Email $email) {
                $email->delete();
            });
        echo "Emails have been cleaned\n";
    }
}
