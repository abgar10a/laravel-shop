<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;

class UploadService {

    public function getFile($id) {
        $upload = Upload::find($id);

        if (!$upload) {
            return ResponseHelper::build(error: 'File not found');
        }

        return ResponseHelper::build('File successfully retrieved', ['url' => asset(Storage::url($upload->path))]);
    }

}
