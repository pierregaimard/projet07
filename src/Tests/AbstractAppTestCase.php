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
        $em = $this->getEntityManager();
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
     * @param User   $user
     *
     * @return mixed
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getToken(Client $client, User $user)
    {
        $response = $client->request('POST', '/login', [
            'json' => [
                'username' => $user->getUsername(),
                'password' => $user->getPassword()
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $response->toArray());

        return $response->toArray()['token'];
    }
}
