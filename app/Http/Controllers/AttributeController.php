<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\AttributeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AttributeController extends Controller
{
    private $attributeService;

    public function __construct()
    {
        $this->attributeService = app(AttributeService::class);
    }

    public function index($type)
    {
        try {
            if ($type) {
                $attrData = $this->attributeService->getAttributesByType($type);
                if (isset($attrData['error'])) {
                    return ResponseHelper::error($attrData['error'], Response::HTTP_NOT_FOUND);
                } else {
                    return ResponseHelper::successData($attrData['message'], $attrData);
                }
            } else {
                return ResponseHelper::error('Wrong type');
            }
        } catch (\Throwable $th) {
            return ResponseHelper::error('Invalid data', Response::HTTP_BAD_REQUEST);
        }
    }

    public function store(Request $request)
    {
        try {
            $attrData = $request->validate([
                'identifier' => 'required|max:30',
                'title' => 'required|max:30',
                'type' => 'required'
            ]);

            $attrResponse = $this->attributeService->createAttribute($attrData);

            if (isset($attrResponse['error'])) {
                return ResponseHelper::error($attrResponse['error'], Response::HTTP_NOT_FOUND);
            } else {
                return ResponseHelper::successData($attrResponse['message'], $attrResponse);
            }
        } catch (\Throwable $th) {
            return ResponseHelper::error('Invalid data', Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $attrData = $request->validate([
                'identifier' => 'nullable|string|max:30',
                'title' => 'nullable|max:30',
            ]);

            $attrData['id'] = $id;

            $attrResponse = $this->attributeService->updateAttribute($attrData);

            if (isset($attrResponse['error'])) {
                return ResponseHelper::error($attrResponse['error'], Response::HTTP_NOT_FOUND);
            } else {
                return ResponseHelper::successData($attrResponse['message'], $attrResponse);
            }
        } catch (\Throwable $th) {
            return ResponseHelper::error('Invalid input', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $attrResponse = $this->attributeService->deleteAttribute($id);
            if (isset($attrResponse['error'])) {
                return ResponseHelper::error($attrResponse['error'], Response::HTTP_NOT_FOUND);
            } else {
                return ResponseHelper::successData($attrResponse['message']);
            }
        } catch (\Throwable $th) {
            return ResponseHelper::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
