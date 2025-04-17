<?php

namespace App\Console\Commands;

use App\Models\Relations\ArticleImageRel;
use App\Models\Upload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command as CommandAlias;

class RemoveUnusedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:cleanup {--force : Confirm cleanup without prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove images that are not related to any article';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $usedUploadIds = ArticleImageRel::pluck('upload_id');

        $unusedUploads = Upload::whereNotIn('id', $usedUploadIds)->get();

        $removeConfirmed = $this->hasOption('force') || $this->ask(
                count($unusedUploads) . " unused images found.\n Do you want to remove them? y/n") === 'y';

        if (!$removeConfirmed) {
            $this->info("Cleaning up canceled.");
            return CommandAlias::SUCCESS;
        } else if (count($unusedUploads) < 1) {
            $this->info("Unused images not found.");
            return CommandAlias::SUCCESS;
        }

        $bar = $this->output->createProgressBar(count($unusedUploads));

        $bar->start();

        foreach ($unusedUploads as $upload) {
            $imagePath = 'app/public/' . $upload->path;

            if (File::exists(storage_path($imagePath))) {
                File::delete(storage_path($imagePath));
                $this->info("Deleted file: " . $imagePath);
            }

            $upload->delete();
            $bar->advance();
        }

        $bar->finish();
        $bar->clear();
        $this->info("Unused uploads removed.");
        return CommandAlias::SUCCESS;
    }
}
