<?php

namespace App\Tests\Authentication;

use App\Tests\AbstractAppTestCase;

final class AuthenticationTest extends AbstractAppTestCase
{
    public function testGetToken()
    {
        $this->refreshDatabase();
        $client = AuthenticationTest::createClient();

        $this->createUser('user', 'pass', ['ROLE_USER'], 'myCompany');

        # No credentials but good Content-Type
        $client->request('POST', '/login');
        $this->assertResponseStatusCodeSame(400);

        # No credentials but good Content-Type
        $client->request('POST', '/login', [
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $this->assertResponseStatusCodeSame(400);

        # Bad credentials keys
        $client->request('POST', '/login', [
            'json' => [
                'myUserName' => 'myUsername',
                'password' => 'myPassword'
            ]
        ]);
        $this->assertResponseStatusCodeSame(400);

        # Empty credentials values
        $client->request('POST', '/login', [
            'json' => [
                'myUserName' => 'myUsername',
                'password' => ''
            ]
        ]);
        $this->assertResponseStatusCodeSame(400);

        # Bad credentials
        $client->request('POST', '/login', [
            'json' => [
                'username' => 'myUsername',
                'password' => 'myPassword'
            ]
        ]);
        $this->assertResponseStatusCodeSame(401);

        # Good credentials
        $response = $client->request('POST', '/login', [
            'json' => [
                'username' => 'user',
                'password' => 'pass'
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $response->toArray());
    }
}
