<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UploadController extends Controller
{

    private $uploadService;

    public function __construct()
    {
        $this->uploadService = app(UploadService::class);
    }

    /**
     * @OA\Post (
     *     path="/uploads",
     *     tags={"Uploads"},
     *     summary="Save file",
     *     description="Save file",
     *     security={{"bearerAuth":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file"},
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="File to upload"
     *                 )
     *             )
     *         )
     *      ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="File saved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Order updated successfully."),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                          type="object",
     *                          @OA\Property(property="order", type="object",
     *                                       @OA\Property(property="id", type="string", example="111"),
     *                                       @OA\Property(property="url", type="string", example="url/for/image"),
     *                                       )
     *                          ),
     *              )
     *         )
     *     ),
     *
     * )
     */
    public function store(Request $request)
    {
        try {
            $uploadData = $request->validate([
                'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $file = $uploadData['file'];

            if (!$file) {
                return ResponseHelper::error('File upload failed');
            }

            $uploadResponse = $this->uploadService->upload($file);

            if (isset($uploadResponse['error'])) {
                return ResponseHelper::error($uploadResponse['error']);
            }

            return ResponseHelper::successData($uploadResponse['message'], $uploadResponse);

        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
