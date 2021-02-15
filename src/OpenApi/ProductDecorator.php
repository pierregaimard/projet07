<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;

class ProductDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ){}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi         = ($this->decorated)($context);
        $schemas         = $openApi->getComponents()->getSchemas();
        $productPathItem = $openApi->getPaths()->getPath('/products');

        $schemas['NotFound.json'] = new ArrayObject([
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
                    'example' => 404,
                ],
                'detail' => [
                    'type' => 'string',
                    'example' => 'Not found',
                ],
            ],
        ]);

        $schemas['NotFound.jsonld'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                '@context' => [
                    'type' => 'string',
                    'example' => '/contexts/Error',
                ],
                '@type' => [
                    'type' => 'string',
                    'example' => 'hydra:Error',
                ],
                'hydra:title' => [
                    'type' => 'string',
                    'example' => 'An error occurred',
                ],
                'detail' => [
                    'type' => 'string',
                    'example' => 'Not found',
                ],
            ],
        ]);

        $responses        = $productPathItem->getGet()->getResponses();
        $responses['404'] = new Response(
            description: 'No product found when using criteria',
            content: new ArrayObject(
                [
                    'application/ld+json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/NotFound.jsonld',
                        ],
                    ],
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/NotFound.json',
                        ],
                    ],
                    'application/hal+json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/NotFound.json',
                        ],
                    ],
                ]
            )
        );

        $get = $productPathItem->getGet()->withResponses($responses);
        $openApi->getPaths()->addPath('/products', $productPathItem->withGet($get));

        return $openApi;
    }
}
