<?php

namespace App\Service\Response;

use Symfony\Component\HttpFoundation\Response;

class CustomErrorResponseManager
{
    public const TYPE_JSON    = 'App\Service\Response\JsonErrorResponse';
    public const TYPE_JSONLD  = 'App\Service\Response\JsonldErrorResponse';
    public const TYPE_JSONHAL = 'App\Service\Response\JsonhalErrorResponse';

    /**
     * @param string $message
     * @param int    $statusCode
     * @param string $type
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getFormattedErrorResponse(string $message, int $statusCode, string $type): Response
    {
        if (!class_exists($type)) {
            throw new InvalidArgumentException(
                sprintf('Class not found for error response type "%s". Did you miss to declare it ?', $type)
            );
        }

        return (new $type())->getResponse($message, $statusCode);
    }

    /**
     * @param string $contentType
     *
     * @return string|false
     */
    public function getErrorType(string $contentType): string|false
    {
        if (str_contains($contentType, 'application/json')) {
            return self::TYPE_JSON;
        }

        if (str_contains($contentType, 'application/ld+json')) {
            return self::TYPE_JSONLD;
        }

        if (str_contains($contentType, 'application/hal+json')) {
            return self::TYPE_JSONHAL;
        }

        return false;
    }
}
