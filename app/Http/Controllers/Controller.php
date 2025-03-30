<?php

namespace App\Http\Controllers;

/**
 * @OA\OpenApi(
 *     security={{"bearerAuth":{}}},
 * )
 *
 * @OA\Server(
 *       url=L5_SWAGGER_CONST_HOST,
 *       description="API Server"
 *  )
 *
 * @OA\Info(
 *       title="PhpShop",
 *       version="1.0.0",
 *       description="API documentation for your Laravel project",
 *       @OA\Contact(
 *           email="ab.xach@gmail.com"
 *       ),
 *       @OA\License(
 *           name="Apache 2.0",
 *           url="https://www.apache.org/licenses/LICENSE-2.0.html"
 *       )
 *  )
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
