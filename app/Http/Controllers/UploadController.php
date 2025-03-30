<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\UploadService;

class UploadController extends Controller
{

    private $uploadService;

    public function __construct() {
        $this->uploadService = app(UploadService::class);
    }

    public function show($uploadId)
    {
        try {
            $fileData = $this->uploadService->getFile($uploadId);

            if (isset($fileData['error'])) {
                return ResponseHelper::error($fileData['error']);
            }

            return ResponseHelper::successData($fileData['message'], $fileData);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage());
        }
    }
}
