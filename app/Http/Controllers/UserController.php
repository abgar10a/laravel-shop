<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    private $userService;

    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    /**
     * @OA\Put (
     *     path="/users/{userId}",
     *     tags={"Users"},
     *     summary="Update user",
     *     description="Update user",
     *     security={{"bearerAuth":{}}},
     *
     *          @OA\Parameter(
     *              name="userId",
     *              in="path",
     *              description="user id",
     *              required=false,
     *              @OA\Schema(
     *                  type="integer",
     *                  default=1,
     *                  example=6
     *              )
     *          ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "email", "user_type", "address", "city", "postal_code", "password"},
     *
     *             @OA\Property(property="name", type="string", example="Robert"),
     *             @OA\Property(property="address", type="string", example="Baghramyan 26"),
     *             @OA\Property(property="city", type="string", example="Yerevan"),
     *             @OA\Property(property="postal_code", type="string", example="80808"),
     *             @OA\Property(property="avatar", type="string", example="123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User updated successfully"),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                         type="object",
     *                         @OA\Property(property="user", type="string", example="user data..."),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="The email has already been taken."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $userData = $request->validate([
                'name' => ['required', 'min:3', 'max:10'],
                'address' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:100'],
                'postal_code' => ['required', 'string', 'max:20'],
                'avatar' => ['nullable', 'exists:uploads,id'],
            ]);

            if ($id != $request->user()->id) {
                return ResponseHelper::error('Unauthorized', Response::HTTP_UNAUTHORIZED);
            }

            $userResponse = $this->userService->updateUser($id, $userData);

            if (isset($userResponse['error'])) {
                return ResponseHelper::error($userResponse['error']);
            }

            return ResponseHelper::successData($userResponse['message'], $userResponse);

        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Delete  (
     *     path="/users/{userId}",
     *     tags={"Users"},
     *     summary="Update user",
     *     description="Update user",
     *     security={{"bearerAuth":{}}},
     *
     *          @OA\Parameter(
     *              name="userId",
     *              in="path",
     *              description="user id",
     *              required=false,
     *              @OA\Schema(
     *                  type="integer",
     *                  default=1,
     *                  example=6
     *              )
     *          ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User deleted successfully"),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="The email has already been taken."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $id)
    {
        try {
            if ($id != $request->user()->id) {
                return ResponseHelper::error('Unauthorized', Response::HTTP_UNAUTHORIZED);
            }

            $userResponse = $this->userService->deleteUser($id);

            if (isset($userResponse['error'])) {
                return ResponseHelper::error($userResponse['error']);
            }

            return ResponseHelper::successData($userResponse['message'], $userResponse);

        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}
