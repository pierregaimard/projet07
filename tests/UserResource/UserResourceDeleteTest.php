<?php

namespace App\Tests\UserResource;

use App\Tests\AbstractUserTestCase;

class UserResourceDeleteTest extends AbstractUserTestCase
{
    public function testDeleteItem()
    {
        $this->refreshDatabase();
        $this->loadUsersFixtures();
        $client = self::createClient();
        $token = $this->getToken($client, 'pgaimard', 'pass');

        # Delete user
        $client->request('DELETE', '/users/2', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertResponseStatusCodeSame(204);

        # Undefined user
        $client->request('DELETE', '/users/244', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertResponseStatusCodeSame(404);
    }
}
