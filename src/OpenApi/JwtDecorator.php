<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;
use ArrayObject;

final class JwtDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated)
    {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['Credentials'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'sthomas',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'mypassword',
                ],
            ],
        ]);

        $schemas['BadRequest'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'type' => [
                    'type' => 'string',
                    'example' => 'https://tools.ietf.org/html/rfc2616#section-10',
                ],
                'title' => [
                    'type' => 'string',
                    'example' => 'An error occurred',
                ],
                'status' => [
                    'type' => 'integer',
                    'example' => 400,
                ],
                'detail' => [
                    'type' => 'string',
                    'example' => 'Invalid request body. You must provide \'username\' and \'password\' keys',
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'JWT Token',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authentication'],
                responses: [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                    '400' => [
                        'description' => 'Bad request',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/BadRequest',
                                ],
                            ],
                        ],
                    ]
                ],
                summary: 'Get JWT token to login.',
                description: 'Retrieves a JWT Token, who has to be passed in Authorization header to be able to retrieve
                BileMo API resources. e.g. : \'Authorization: Bearer JWT_TOKEN\'',
                requestBody: new Model\RequestBody(
                    description: 'Generate new JWT Token',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $openApi->getPaths()->addPath('/login', $pathItem);

        return $openApi;
    }
}
