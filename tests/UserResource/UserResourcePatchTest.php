<?php

namespace App\Tests\UserResource;

use App\Tests\AbstractUserTestCase;

class UserResourcePatchTest extends AbstractUserTestCase
{
    public function testUserItemChangeUsername()
    {
        $this->refreshDatabase();
        $this->loadUsersFixtures();
        $client = self::createClient();
        $token = $this->getToken($client, 'pgaimard', 'pass');

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
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'username' => 'newUsername'
        ]);
    }

    public function testUserItemChangePassword()
    {
        $this->refreshDatabase();
        $this->loadUsersFixtures();
        $client = self::createClient();
        $token = $this->getToken($client, 'pgaimard', 'pass');

        $client->request('PATCH', '/users/2', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/merge-patch+json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'username' => 'test',
                'password' => 'myNew$seccuredPassword1234',
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->getToken($client, 'test', 'myNew$seccuredPassword1234');
    }
}
