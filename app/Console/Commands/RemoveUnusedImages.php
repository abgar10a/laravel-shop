<?php

namespace App\Console\Commands;

use App\Models\Relations\ArticleImageRel;
use App\Models\Upload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RemoveUnusedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:cleanup';

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

        foreach ($unusedUploads as $upload) {
            $imagePath = 'app/public/' . $upload->path;

            if (File::exists(storage_path($imagePath))) {
                File::delete(storage_path($imagePath));
                $this->info("Deleted file: " . $imagePath);
            }

            $upload->delete();
            $this->info("Deleted DB entry: ID " . $upload->id);
        }

        $this->info("Unused uploads removed.");

    }
}
