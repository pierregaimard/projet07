<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Customer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Contracts\HttpClient\Exception\{
    ClientExceptionInterface,
    DecodingExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface
};

abstract class AbstractAppTestCase extends ApiTestCase
{
    protected function refreshDatabase()
    {
        static::bootKernel();
        $em       = $this->getEntityManager();
        $metaData = $em->getMetadataFactory()->getAllMetadata();

        $tool = new SchemaTool($em);
        $tool->dropSchema($metaData);
        $tool->createSchema($metaData);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return static::$container->get('doctrine')->getManager();
    }

    /**
     * @param string $name
     *
     * @return Customer
     */
    protected function createCustomer(string $name): Customer
    {
        $customer = new Customer();
        $customer->setName($name);

        $em = $this->getEntityManager();
        $em->persist($customer);
        $em->flush();

        return $customer;
    }

    /**
     * @param string          $username
     * @param string          $password
     * @param array           $roles
     * @param Customer|string $customer
     *
     * @return User
     */
    protected function createUser(string $username, string $password, array $roles, Customer|string $customer): User
    {
        if (!$customer instanceof Customer) {
            $customer = $this->createCustomer($customer);
        }

        $user = new User();
        $user->setUsername($username);
        $user->setEmail(sprintf('%s@test.fr', $username));
        $user->setPassword(static::$container->get('security.password_encoder')->encodePassword($user, $password));
        $user->setRoles($roles);
        $user->setCustomer($customer);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @param Client $client
     * @param string $username
     * @param string $password
     *
     * @return mixed
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getToken(Client $client, string $username, string $password)
    {
        $response = $client->request('POST', '/login', [
            'json' => [
                'username' => $username,
                'password' => $password
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $response->toArray());

        return $response->toArray()['token'];
    }

    protected function createUserAndGetToken(
        Client $client,
        string $username,
        string $password,
        array $roles,
        Customer|string $customer
    ) {
        $this->createUser($username, $password, $roles, $customer);

        return $this->getToken($client, $username, $password);
    }

    /**
     * @param Client $client
     * @param string $method
     * @param string $url
     * @param string $token
     *
     * @throws TransportExceptionInterface
     */
    protected function assertMethodNotAllowed(Client $client, string $method, string $url, string $token)
    {
        $client->request($method, $url, [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);

        $this->assertResponseStatusCodeSame(405);
    }

    /**
     * @param Client $client
     * @param string $method
     * @param string $url
     * @param string $token
     *
     * @throws TransportExceptionInterface
     */
    protected function assertMethodAllowed(Client $client, string $method, string $url, string $token)
    {
        $client->request($method, $url, [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);

        $this->assertResponseIsSuccessful();
    }
}
