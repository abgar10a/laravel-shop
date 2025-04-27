<?php

use App\Models\Email;
use Illuminate\Support\Facades\Schedule;

Schedule::command('images:cleanup --force')->weekly();

//Schedule::command(RemoveUnusedEmails::class)->weekly();

Schedule::command('model:prune', [
    '--model' => [Email::class],
])->weekly();

Schedule::command('ratings:update --users --articles')->dailyAt('12:00');
