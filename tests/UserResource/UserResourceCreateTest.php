<?php

namespace App\Tests\UserResource;

use App\Tests\AbstractUserTestCase;

class UserResourceCreateTest extends AbstractUserTestCase
{
    public function testRequest()
    {
        $this->refreshDatabase();
        $client = self::createClient();
        $token = $this->createUserAndGetToken($client, 'admin', 'pass', ['ROLE_ADMIN'], 'company');

        # No body provided but good Content-Type
        $client->request('POST', '/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(400);

        # No Content-Type and no body provided
        $client->request('POST', '/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $this->assertResponseStatusCodeSame(415);

        # Empty body
        $client->request('POST', '/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testGoodData()
    {
        $this->refreshDatabase();
        $client = self::createClient();
        $token = $this->createUserAndGetToken($client, 'admin', 'pass', ['ROLE_ADMIN'], 'company');

        # Good user information
        $client->request('POST', '/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ],
            'json' => [
                'username' => 'test',
                'email' => 'test@test.com',
                'password' => 'myFavoritePassword$154',
                'isAdmin' => true
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['username' => 'test', 'isAdmin' => true]);

        # Should return the user id
        $this->assertJsonContains(['id' => 2]);
    }

    public function testBadData()
    {
        $this->refreshDatabase();
        $client = self::createClient();
        $token = $this->createUserAndGetToken($client, 'admin', 'pass', ['ROLE_ADMIN'], 'company');

        # Bad user information
        $response = $client->request('POST', '/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'username' => 'te',
                'email' => 'test@test',
                'password' => 'myFavoritePassword',
                'isAdmin' => 'toto'
            ]
        ]);
        $this->assertResponseStatusCodeSame(422);
        $violations = $response->toArray(false)['violations'];

        # Four violations
        $this->assertEquals(4, count($violations));

        $this->assertEquals('Username must be longer than 3 characters', $violations[0]['message']);
        $this->assertEquals('This value is not a valid email address.', $violations[1]['message']);
        $this->assertEquals('This password is not safe.', $violations[2]['message']);
        $this->assertEquals('This value should be of type bool.', $violations[3]['message']);
    }

    public function testUserDuplication()
    {
        $this->refreshDatabase();
        $this->loadUsersFixtures();
        $client = self::createClient();
        $token = $this->getToken($client, 'pgaimard', 'pass');

        $response = $client->request('POST', '/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'username' => 'pgaimard',
                'email' => 'pierre@flexcon.fr',
                'password' => 'myFavoritePassword$154',
                'isAdmin' => true
            ]
        ]);
        $this->assertResponseStatusCodeSame(422);
        $violations = $response->toArray(false)['violations'];

        # Two violations
        $this->assertEquals(2, count($violations));

        $this->assertEquals('This value is already used.', $violations[0]['message']);
        $this->assertEquals('This value is already used.', $violations[1]['message']);
    }
}
