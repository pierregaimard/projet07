<?php

namespace App\Tests\Resource;

use App\Tests\AbstractProductTestCase;

class ProductResourceMethodsTest extends AbstractProductTestCase
{
    public function testMethodsNotAllowed()
    {
        $this->refreshDatabase();
        $this->setProductsFixtures();
        $client = self::createClient();
        $token = $this->createUserAndGetToken($client, 'test', 'pass', ['ROLE_USER'], 'myCompagny');

        # Collection Method
        $this->assertMethodNotAllowed($client, 'POST', '/products', $token);

        # Item methods
        $this->assertMethodNotAllowed($client, 'PUT', '/products/1', $token);
        $this->assertMethodNotAllowed($client, 'DELETE', '/products/1', $token);
        $this->assertMethodNotAllowed($client, 'PATCH', '/products/1', $token);
    }

    public function testMethodsAllowed()
    {
        $this->refreshDatabase();
        $this->setProductsFixtures();
        $client = self::createClient();
        $token = $this->createUserAndGetToken($client, 'test', 'pass', ['ROLE_USER'], 'myCompagny');

        # Collection Method
        $this->assertMethodAllowed($client, 'GET', '/products', $token);

        # Item method
        $this->assertMethodAllowed($client, 'GET', '/products/1', $token);
    }
}
