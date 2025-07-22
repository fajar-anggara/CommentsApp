<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="CommentsApp API Documentation",
 *     description="Automatically generated documentation for the finished CommentApp endpoints"
 * )
 *
 * @OA\Server(
 *     url="/",
 *     description="Current host"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Enter token in format (Bearer <token>)"
 * )
 */
class OpenApi {}
