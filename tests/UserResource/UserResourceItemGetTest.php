<?php

namespace App\Tests\UserResource;

use App\Tests\AbstractUserTestCase;

class UserResourceItemGetTest extends AbstractUserTestCase
{
    public function testGetUserItem()
    {
        $this->refreshDatabase();
        $client = self::createClient();
        $token = $this->createUserAndGetToken($client, 'admin', 'pass', ['ROLE_ADMIN'], 'company');

        $response = $client->request('GET', '/users/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        # Id is not returned for item request
        $this->assertArrayNotHasKey('id', $response->toArray());
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/users/1',
            '@type' => 'User',
            'username' => 'admin',
            'email' => 'admin@test.fr',
            'isAdmin' => true
        ]);
    }
}
