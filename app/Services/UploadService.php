<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;

class UploadService
{

    public function upload($file)
    {
        try {
            $uploadPath = $file->store('articleImages', 'public');

            $upload = Upload::create([
                'type' => $file->getClientMimeType(),
                'path' => $uploadPath,
            ]);

            return ResponseHelper::build('File saved successfully', [
                'id' => $upload->id,
                'url' => asset(Storage::url($upload->path)),
            ]);
        } catch (\Throwable $th) {
            return ResponseHelper::build(error: 'Error while saving file');
        }
    }

}
