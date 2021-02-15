<?php

namespace App\Tests\Resource;

use App\Tests\AbstractProductTestCase;

class ProductResourceAuthorizationTest extends AbstractProductTestCase
{
    public function testGetAuthorization()
    {
        $this->refreshDatabase();
        $this->setProductsFixtures();
        $client = self::createClient();

        # Unauthorized access for not authenticated requests
        $client->request('GET', '/products');
        $this->assertResponseStatusCodeSame(401);

        $token = $this->createUserAndGetToken($client, 'test', 'pass', ['ROLE_USER'], 'myCompagny');

        # Authorized collection request
        $client->request('GET', '/products', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertResponseStatusCodeSame(200);

        # Authorized item request
        $client->request('GET', '/products/1', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertResponseStatusCodeSame(200);
    }
}
