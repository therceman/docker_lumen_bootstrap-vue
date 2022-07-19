<?php

namespace App\Http\Controllers;

use App\DTO\BaseDTO;
use App\DTO\ErrorDTO;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class ApiController extends BaseController
{
    /**
     * @OA\Info(
     *   title="App Api",
     *   version="1.0",
     *   description="This is an API for App"
     * )
     */

    /**
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="apiKey",
     *     name="Authorization",
     *     in="header",
     * )
     */

    /**
     * @param BaseDTO $res
     * @return JsonResponse
     */
    public function jsonResponse($res): JsonResponse
    {
        return response()->json($res);
    }
}