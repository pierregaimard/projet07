<?php

namespace App\Tests\ProductResource;

use App\Entity\Product;
use App\Tests\AbstractProductTestCase;

final class ProductResourceItemTest extends AbstractProductTestCase
{
    public function testGetItem()
    {
        $this->refreshDatabase();
        $client = self::createClient();

        # Create new product
        $product = new Product();
        $product->setName('myProduct');
        $product->setDescription('This is an amazing product');
        $product->setPrice(200);
        $product->setColor('red');
        $em = $this->getEntityManager();
        $em->persist($product);
        $em->flush();

        $this->createUser('test', 'pass', ['ROLE_USER'], 'myCompagny');
        $token = $this->getToken($client, 'test', 'pass');

        # Authorized user request
        $client->request('GET', '/products/' . $product->getId(), [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertResponseStatusCodeSame(200);

        # application/ld+json returned content
        $client->request('GET', '/products/' . $product->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json'
            ]
        ]);
        $this->assertJsonContains([
            '@context' => '/contexts/Product',
            '@id' => '/products/' . $product->getId(),
            '@type' => 'Product',
            'name' => 'myProduct',
            'description' => 'This is an amazing product',
            'color' => 'red',
            'price' => 200
        ]);

        # application/json returned content
        $client->request('GET', '/products/' . $product->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);
        $this->assertJsonContains([
            'name' => 'myProduct',
            'description' => 'This is an amazing product',
            'color' => 'red',
            'price' => 200
        ]);

        # application/hal+json returned content
        $response = $client->request('GET', '/products/' . $product->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/hal+json'
            ]
        ]);
        $this->assertJsonContains([
            '_links' => [
                'self' => [
                    'href' => '/products/' . $product->getId()
                ]
            ],
            'name' => 'myProduct',
            'description' => 'This is an amazing product',
            'color' => 'red',
            'price' => 200
        ]);

        # Id is never returned
        $this->assertArrayNotHasKey('id', $response->toArray());

        # not found response
        $client->request('GET', '/products/3', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json'
            ]
        ]);
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains(['hydra:description' => 'Not Found']);
    }
}
