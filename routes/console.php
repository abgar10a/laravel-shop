<?php

use App\Console\Commands\RemoveUnusedEmails;
use App\Console\Commands\RemoveUnusedImages;
use Illuminate\Support\Facades\Schedule;

Schedule::command(RemoveUnusedImages::class)->weekly();
Schedule::command(RemoveUnusedEmails::class)->weekly();
