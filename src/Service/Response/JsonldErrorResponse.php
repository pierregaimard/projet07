<?php

namespace App\Service\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonldErrorResponse implements ErrorResponseInterface
{
    public function getResponse(string $message, int $statusCode): Response
    {
        return new JsonResponse(
            [
                '@context' => '/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'An error occurred',
                'hydra:description' => $message
            ],
            $statusCode,
            [
                'Content-Type' => 'application/ld+json'
            ]
        );
    }
}
