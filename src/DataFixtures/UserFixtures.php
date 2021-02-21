<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $adminOne = new User();
        $adminOne->setUsername('pgaimard');
        $adminOne->setEmail('pierre@flexcon.fr');
        $adminOne->setPassword($this->encoder->encodePassword($adminOne, 'pass'));
        $adminOne->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $adminOne->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_FLEXCON));
        $manager->persist($adminOne);

        $userOne = new User();
        $userOne->setUsername('sthomas');
        $userOne->setEmail('stephane@flexcon.fr');
        $userOne->setPassword($this->encoder->encodePassword($adminOne, 'pass'));
        $userOne->setRoles(['ROLE_USER']);
        $userOne->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_FLEXCON));
        $manager->persist($userOne);

        $adminTwo = new User();
        $adminTwo->setUsername('jsanza');
        $adminTwo->setEmail('jeff@best-platform.fr');
        $adminTwo->setPassword($this->encoder->encodePassword($adminOne, 'pass'));
        $adminTwo->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $adminTwo->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_BEST_PLATFORM));
        $manager->persist($adminTwo);

        $userTwo = new User();
        $userTwo->setUsername('nflorio');
        $userTwo->setEmail('nathalie@best-platform.fr');
        $userTwo->setPassword($this->encoder->encodePassword($adminOne, 'pass'));
        $userTwo->setRoles(['ROLE_USER']);
        $userTwo->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_BEST_PLATFORM));
        $manager->persist($userTwo);

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            CustomerFixtures::class,
        ];
    }
}
