<?php

namespace App\Http\Controllers;

/**
 * @OA\OpenApi(
 *     security={{"bearerAuth":{}}},
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your JWT token in the format: 'Bearer {token}'"
 * )
 */
abstract class Controller
{
    //
}
