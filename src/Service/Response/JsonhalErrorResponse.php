<?php

namespace App\Service\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonhalErrorResponse implements ErrorResponseInterface
{
    public function getResponse(string $message, int $statusCode): Response
    {
        return new JsonResponse(
            [
                'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
                'title' => 'An error occurred',
                'status' => $statusCode,
                'detail' => $message
            ],
            $statusCode
        );
    }
}
