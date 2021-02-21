<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;

class UserDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ){}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi       = ($this->decorated)($context);
        $schemas       = $openApi->getComponents()->getSchemas();
        $usersPathItem = $openApi->getPaths()->getPath('/users');
        $userPathItem  = $openApi->getPaths()->getPath('/users/{id}');

        # Unprocessable entity schema
        $schemas['UnprocessableEntity'] = new ArrayObject([
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
                'detail' => [
                    'type' => 'string',
                    'example' => 'password: This password is not safe.',
                ],
                'violations' => [
                    'type' => 'array',
                    'example' => [
                        'propertyPath' => 'password',
                        'message' => 'This password is not safe.',
                        'code' => 'de1e3db3-5ed4-4941-aae4-59f3667cc3a3'
                    ]
                ]
            ],
        ]);

        # Remove unused schemas
        unset($schemas['User']);
        unset($schemas['User.jsonld']);
        unset($schemas['Customer']);
        unset($schemas['Customer.jsonld']);

        # --- Add custom responses to Swagger docs ---

        # Collection > get
        $getCollectionResponses        = $usersPathItem->getGet()->getResponses();
        $getCollectionResponses['403'] = new Response(
            description: 'Access forbidden when the logged user is not an admin'
        );
        $getCollectionResponses['404'] = new Response(
            description: 'No user found when using pagination or search criteria'
        );

        $get = $usersPathItem->getGet()->withResponses($getCollectionResponses);

        # Collection > post
        $postCollectionResponses        = $usersPathItem->getPost()->getResponses();
        $postCollectionResponses['403'] = new Response(
            description:'Access forbidden when the logged user is not an admin'
        );
        $postCollectionResponses['422'] = new Response(
            description: 'Unprocessable entity. Request was well-formed but failed due to data constraints validations',
            content: new ArrayObject(
                ['application/json' => ['schema' => ['$ref' => '#/components/schemas/UnprocessableEntity']]]
            )
        );

        $post = $usersPathItem->getPost()->withResponses($postCollectionResponses);

        # add collection data to openApi
        $openApi->getPaths()->addPath(
            '/users',
            $usersPathItem
                ->withGet($get)
                ->withPost($post)
        );

        # Item > get
        $getItemResponses        = $userPathItem->getGet()->getResponses();
        $getItemResponses['403'] = new Response(
            description: 'Access forbidden when the logged user is not an admin or if required resource is from an other
                company'
        );

        $get = $userPathItem->getGet()->withResponses($getItemResponses);

        # Item > delete
        $deleteItemResponses        = $userPathItem->getDelete()->getResponses();
        $deleteItemResponses['403'] = new Response(
            description: 'Access forbidden when the logged user is not an admin, if the resource you want to delete is
                from an other company or if the resource is the logged user'
        );

        $delete = $userPathItem->getDelete()->withResponses($deleteItemResponses);

        # Item > patch
        $patchItemResponses        = $userPathItem->getPatch()->getResponses();
        $patchItemResponses['403'] = new Response(
            description:'Access forbidden when the logged user is not an admin or if required resource is from an other
                company'
        );
        $patchItemResponses['422'] = new Response(
            description: 'Unprocessable entity. Request was well-formed but failed due to data constraints validations',
            content: new ArrayObject(
                ['application/json' => ['schema' => ['$ref' => '#/components/schemas/UnprocessableEntity']]]
            )
        );

        $patch = $userPathItem->getPatch()->withResponses($patchItemResponses);

        # add item data to openApi
        $openApi->getPaths()->addPath(
            '/users/{id}',
            $userPathItem
                ->withGet($get)
                ->withDelete($delete)
                ->withPatch($patch)
        );

        return $openApi;
    }
}
