<?php

namespace App\ResourceConfig;

class UserOperations
{
    # ------ collectionOperations ------

    # GET
    public const COLLECTION_OPERATIONS_GET = [
        'normalization_context' => [
            'groups' => ['users:read', 'user:read'],
            'swagger_definition_name' => 'Collection_Read',
        ],
        'security' => 'is_granted("ROLE_ADMIN")',
        'security_message' => 'You must be an admin to get access or to handle User resources',
        'openapi_context' => [
            'summary' => 'Retrieves the collection of users from your company',
        ],
    ];

    # POST
    public const COLLECTION_OPERATIONS_POST = [
        'denormalization_context' => [
            'groups' => ['user:write'],
            'swagger_definition_name' => 'Create',
        ],
        'normalization_context' => [
            'groups' => ['users:read', 'user:read'],
            'swagger_definition_name' => 'Created_Read',
        ],
        'openapi_context' => [
            'summary' => 'Creates a new user resource for your company',
            'description' =>    'Creates a User resource for your company. (There is just two possibles roles for a 
                                     new user : "user" or "admin". Set "isAdmin" option to true if you want your new
                                     user to become an admin or simply omit it if you don\'t.)',
        ],
        'validation_groups' => ['Default', 'user:create'], # For plainPassword field
        'input_formats' => ['json' => ['application/json']],
    ];


    # ------ itemsOperations ------

    # GET
    public const ITEM_OPERATIONS_GET = [
        'normalization_context' => [
            'groups' => ['user:read'],
            'swagger_definition_name' => 'Item_Read',
        ],
        'security' => 'is_granted("USER_READ", object)',
        'security_message' => 'You must be an admin to access to User Resources and it must be from your own company',
        'openapi_context' => [
            'summary' => 'Retrieves the the detail of a user resource',
        ]
    ];

    # PATCH
    public const ITEM_OPERATIONS_PATCH = [
        'denormalization_context' => [
            'groups' => ['user:write'],
            'swagger_definition_name' => 'Update',
        ],
        'normalization_context' => [
            'groups' => ['user:read'],
            'swagger_definition_name' => 'Item_Read',
        ],
        'security' => 'is_granted("USER_UPDATE", object)',
        'security_message' => 'You must be an admin to modify a User Resources and it must be from your own company',
    ];

    # DELETE
    public const ITEM_OPERATIONS_DELETE = [
        'security' => 'is_granted("USER_DELETE", object)',
        'security_message' => 'You must be an admin to delete a User Resources and you can\'t delete the logged user',
    ];
}
