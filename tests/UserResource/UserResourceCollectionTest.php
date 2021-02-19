<?php

namespace App\Tests\UserResource;

use App\Tests\AbstractUserTestCase;

final class UserResourceCollectionTest extends AbstractUserTestCase
{
    public function testGetCollection()
    {
        $this->refreshDatabase();
        $this->loadUsersFixtures();
        $client = self::createClient();
        $token = $this->getToken($client, 'pgaimard', 'pass');

        # Should return only users from same compagny as pgaimard
        $response = $client->request('GET', '/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);
        $this->assertEquals(2, count($response->toArray()));
        $firstUser = $response->toArray()[0];
        # Id is returned only for GET collection request
        $this->assertArrayHasKey('id', $firstUser);

        # Search filter
        $client->request('GET', '/users?username=tho', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);
        $this->assertJsonContains([[
            'username' => 'sthomas'
        ]]);

        # Pagination
        $response = $client->request('GET', '/users?itemsPerPage=1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);
        $this->assertEquals(1, count($response->toArray()));
    }
}