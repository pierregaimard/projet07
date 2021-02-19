<?php

namespace App\Tests\UserResource;

use App\Tests\AbstractUserTestCase;

class UserResourceAuthorizationTest extends AbstractUserTestCase
{
    public function testGetItemUserAuthorization()
    {
        $this->refreshDatabase();
        $this->loadUsersFixtures();
        $client = self::createClient();

        $token = $this->createUserAndGetToken($client, 'test', 'pass', ['ROLE_USER'], 'test');
        # Forbidden to make operations for users with ROLE_USER.

        # Authorized to retrieve user from logged user company.
        $client->request('GET', '/users/1', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testGetItemAdminAuthorization()
    {
        $this->refreshDatabase();
        $this->loadUsersFixtures();
        $client = self::createClient();

        $token = $this->getToken($client, 'pgaimard', 'pass');

        # Forbidden: retrieve user from another company.
        $client->request('GET', '/users/4', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertResponseStatusCodeSame(403);

        # Authorized to retrieve user from same company as logged user.
        $client->request('GET', '/users/1', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCollectionAuthorization()
    {
        $this->refreshDatabase();
        $this->loadUsersFixtures();
        $client = self::createClient();

        # Standard user
        $token = $this->getToken($client, 'sthomas', 'pass');

        # Access forbidden for users with ROLE_USER
        $client->request('GET', '/users', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertResponseStatusCodeSame(403);

        # Admin user
        $token = $this->getToken($client, 'pgaimard', 'pass');

        # Access authorized for users with ROLE_ADMIN
        $client->request('GET', '/users', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testAddUser()
    {
        $this->refreshDatabase();
        $client = self::createClient();

        $token = $this->createUserAndGetToken($client, 'user', 'pass', ['ROLE_USER'], 'company');

        # Access forbidden for users with ROLE_USER
        $client->request('POST', '/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testPatchUser()
    {
        $this->refreshDatabase();
        $this->loadUsersFixtures();
        $client = self::createClient();

        $token = $this->getToken($client, 'pgaimard', 'pass');

        # Try to modify user from another company
        $client->request('PATCH', '/users/3', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/merge-patch+json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'username' => 'newUsername'
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);

        $token = $this->getToken($client, 'sthomas', 'pass');

        # Try to modify user with ROLE_USER account
        $client->request('PATCH', '/users/2', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/merge-patch+json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'username' => 'newUsername'
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteUser()
    {
        $this->refreshDatabase();
        $this->loadUsersFixtures();
        $client = self::createClient();

        $token = $this->getToken($client, 'pgaimard', 'pass');

        # Try to delete user from another company
        $client->request('DELETE', '/users/3', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);

        # Try to delete logged user
        $client->request('DELETE', '/users/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);

        # Try to delete user with ROLE_USER account
        $token = $this->getToken($client, 'sthomas', 'pass');
        $client->request('DELETE', '/users/2', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
    }
}
