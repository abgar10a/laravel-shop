<?php

use App\Console\Commands\RemoveUnusedEmails;
use App\Console\Commands\RemoveUnusedImages;
use App\Models\Email;
use Illuminate\Support\Facades\Schedule;

Schedule::command('images:cleanup --force')->everyFiveSeconds();
//Schedule::command(RemoveUnusedEmails::class)->weekly();
Schedule::command('model:prune', [
    '--model' => [Email::class],
])->weekly();
