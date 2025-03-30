<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Helpers\EmailHelper;
use App\Helpers\ResponseHelper;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    private $authService;

    public function __construct()
    {
        $this->authService = app(AuthService::class);
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Auth"},
     *     summary="User login",
     *     description="Login with email and password and return token",
     *     security={},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User logged in."),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                          type="object",
     *                          @OA\Property(property="token", type="string", example="ddqwdehwd87287f2dh82hqddd82"),
     *                          @OA\Property(property="refresh_token", type="string", example="dehwd872sadasfaf872dh82hd82"),
     *              )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="The provided credentials are incorrect."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $result = $this->authService->login($credentials);

            if (isset($result['error'])) {
                return ResponseHelper::error($result['error']);
            }

            return ResponseHelper::successData('Logged in successfully.', $result);
        } catch (\Throwable $th) {
            return ResponseHelper::error('Something went wrong while logging in.', Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     tags={"Auth"},
     *     summary="User register",
     *     description="Register with user info and get logged in",
     *     security={},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "email", "user_type", "address", "city", "postal_code", "password"},
     *
     *             @OA\Property(property="name", type="string", example="Robert"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="user_type", type="string", example="i"),
     *             @OA\Property(property="address", type="string", example="Baghramyan 26"),
     *             @OA\Property(property="city", type="string", example="Yerevan"),
     *             @OA\Property(property="postal_code", type="string", example="80808"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful register",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                         type="object",
     *                         @OA\Property(property="token", type="string", example="ddqwdehwd87287f2dh82hqddd82"),
     *                         @OA\Property(property="refresh_token", type="string", example="dehwd872sadasfaf872dh82hd82"),
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
    public function register(Request $request)
    {
        try {
            $userData = $request->validate([
                'name' => ['required', 'min:3', 'max:10'],
                'email' => ['required', 'email', Rule::unique('users', 'email')],
                'user_type' => ['required', Rule::in(UserTypes::cases())],
                'address' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:100'],
                'postal_code' => ['required', 'string', 'max:20'],
                'password' => ['required', 'min:8', 'max:30']
            ]);

            $result = $this->authService->register($userData);

            if (isset($result['error'])) {
                return ResponseHelper::error($result['error']);
            }

            return ResponseHelper::successData($result['message'], $result);

        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    // Get Authenticated User
    public function me()
    {
        return response()->json($this->authService->getAuthenticatedUser());
    }

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"Auth"},
     *     summary="User logout",
     *     description="Logout",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User logged out successfully"),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthorized."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        try {
            $logoutResponse = $this->authService->logout();

            return ResponseHelper::success($logoutResponse['message']);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/forgot-password",
     *     tags={"Auth"},
     *     summary="Forgot password",
     *     description="Password recovery step",
     *     security={},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"email"},
     *
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Confirmation code sent",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Email confirmation sent"),
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
     *             @OA\Property(property="message", type="string", example="Invalid email."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     )
     * )
     */
    public function forgotPassword(Request $request)
    {
        try {
            $requestData = $request->validate(['email' => 'required|email']);

            $reset = $this->authService->forgotPassword($requestData['email']);

            if (isset($reset['error'])) {
                return ResponseHelper::error($reset['error'], Response::HTTP_NOT_FOUND);
            } else {
                EmailHelper::sendEmail($reset['user'], 'Email confirmation', ['code' => $reset['code']], 'email_confirmation');

                return ResponseHelper::success('Email confirmation sent', Response::HTTP_OK);
            }
        } catch (\Throwable $th) {
            return ResponseHelper::error("Invalid email");
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/confirm-code",
     *     tags={"Auth"},
     *     summary="Confirm recovery code",
     *     description="Email confirmation",
     *     security={},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"email"},
     *
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Code confirmed",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Email confirmed"),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid code",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid code."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Code expired",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Code expired."),
     *              @OA\Property(property="error", type="boolean", example="true")
     *          )
     *      )
     * )
     */
    public function confirmCode(Request $request)
    {
        try {
            $requestData = $request->validate([
                'email' => 'required|email',
                'code' => 'required|digits:6'
            ]);

            $confirm = $this->authService->confirmCode($requestData['email'], $requestData['code']);

            if (isset($confirm['error'])) {
                return ResponseHelper::error($confirm['error'], Response::HTTP_NOT_FOUND);
            } else {
                return ResponseHelper::success($confirm['message'], Response::HTTP_OK);
            }
        } catch (\Throwable $th) {
            return ResponseHelper::error("Invalid code", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/reset-password",
     *     tags={"Auth"},
     *     summary="Password reset",
     *     description="Password reset",
     *     security={},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password")
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successful.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Password reset successful."),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User not found."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     ),
     *
     *     @OA\Response(
     *          response=400,
     *          description="Code expired",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="Invalid input."),
     *              @OA\Property(property="error", type="boolean", example="true")
     *          )
     *      )
     * )
     */
    public function resetPassword(Request $request)
    {

        try {
            $resetData = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:8|max:30'
            ]);

            $reset = $this->authService->resetPassword($resetData['email'], $resetData['password']);

            if (isset($reset['error'])) {
                return ResponseHelper::error($reset['error'], Response::HTTP_NOT_FOUND);
            } else {
                EmailHelper::sendEmail($reset['user'], 'Email confirmation', [], 'password_reset');

                return ResponseHelper::success($reset['message'], Response::HTTP_OK);
            }
        } catch (\Throwable $th) {
            return ResponseHelper::error("Invalid input");
        }
    }
}
