<?php

namespace App\Service\Response;

use Symfony\Component\HttpFoundation\Response;

interface ErrorResponseInterface
{
    public function getResponse(string $message, int $statusCode): Response;
}
