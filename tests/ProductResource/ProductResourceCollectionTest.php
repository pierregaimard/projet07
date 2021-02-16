<?php

namespace App\Tests\ProductResource;

use App\Tests\AbstractProductTestCase;

class ProductResourceCollectionTest extends AbstractProductTestCase
{
    public function testGetCollection()
    {
        $this->refreshDatabase();
        $this->loadProductsFixtures();
        $client = self::createClient();
        $token = $this->createUserAndGetToken($client, 'test', 'pass', ['ROLE_USER'], 'myCompagny');

        # Default response content check
        $response = $client->request('GET', '/products', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json'
            ]
        ]);
        $this->assertJsonContains([
            '@context' => '/contexts/Product',
            '@id' => '/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 25,
        ]);
        $data = $response->toArray();
        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertArrayHasKey('hydra:view', $data);
        $this->assertArrayHasKey('hydra:search', $data);
    }

    public function testGetCollectionWithParams()
    {
        $this->refreshDatabase();
        $this->loadProductsFixtures();
        $client = self::createClient();
        $this->createUser('test', 'pass', ['ROLE_USER'], 'myCompagny');
        $token = $this->getToken($client, 'test', 'pass');

        # ItemsPerPage parameter check
        $response = $client->request('GET', '/products?itemsPerPage=5', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);
        $data = $response->toArray();
        $this->assertEquals(5, count($data));

        # Price range parameter check
        $response = $client->request('GET', '/products?price[between]=500..1000', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json'
            ]
        ]);
        $data = $response->toArray();
        $this->assertEquals(4, count($data['hydra:member']));

        # Price order check
        $response = $client->request('GET', '/products?order[price]=desc', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);
        $data = $response->toArray();
        $this->assertEquals(1249, $data[0]['price']);

        # Name search & order check
        $response = $client->request('GET', '/products?name=X4&order[name]=desc', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/hal+json'
            ]
        ]);
        $data = $response->toArray();
        $this->assertEquals('BileMo X4-LG20 Test White', $data['_embedded']['item'][0]['name']);

        # Search with empty result
        $client->request('GET', '/products?page=10', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertResponseStatusCodeSame(404);
    }
}
