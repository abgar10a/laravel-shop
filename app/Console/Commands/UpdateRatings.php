<?php

namespace App\Console\Commands;

use App\Jobs\UpdateRatingsJob;
use App\Models\Article;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateRatings extends Command
{
    protected $signature = 'ratings:update
                            {--user : Update ratings for users}
                            {--article : Update ratings for articles}';

    protected $description = 'Update ratings';

    public function handle()
    {
        if (!$this->option('article') && !$this->option('user')) {
            if ($this->confirm('Do you want to update ratings for both articles and users?')) {
                $this->info('Updating ratings for both articles and users...');
                $this->updateRatingsForArticles();
                $this->updateRatingsForUsers();
            } else {
                $this->info('No updates made.');
            }
            return;
        }

        if ($this->option('article')) {
            $this->info('Updating ratings for articles...');
            $this->updateRatingsForArticles();
        }

        if ($this->option('user')) {
            $this->info('Updating ratings for users...');
            $this->updateRatingsForUsers();
        }

        $this->info('Ratings update jobs dispatched.');
    }

    protected function updateRatingsForArticles()
    {
        Article::chunk(100, function ($articles) {
            dispatch(new UpdateRatingsJob($articles->pluck('id'), Article::class, 'articles'));
        });
    }

    protected function updateRatingsForUsers()
    {
        User::chunk(100, function ($users) {
            dispatch(new UpdateRatingsJob($users->pluck('id')->toArray(), User::class, 'users'));
        });
    }
}
