<?php

namespace App\Tests;

use App\Entity\Customer;
use App\Entity\User;

class AbstractUserTestCase extends AbstractAppTestCase
{
    public function loadUsersFixtures()
    {
        $manager   = $this->getEntityManager();
        $pwEncoder = static::$container->get('security.password_encoder');

        # Customer fixtures
        $customerOne = new Customer();
        $customerOne->setName('Flexcon Corp');
        $manager->persist($customerOne);

        $customerTwo = new Customer();
        $customerTwo->setName('My Best Platform');
        $manager->persist($customerTwo);

        #Users Fixtures
        $adminOne = new User();
        $adminOne->setUsername('pgaimard');
        $adminOne->setEmail('pierre@flexcon.fr');
        $adminOne->setPassword($pwEncoder->encodePassword($adminOne, 'pass'));
        $adminOne->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $adminOne->setCustomer($customerOne);
        $manager->persist($adminOne);

        $userOne = new User();
        $userOne->setUsername('sthomas');
        $userOne->setEmail('stephane@flexcon.fr');
        $userOne->setPassword($pwEncoder->encodePassword($adminOne, 'pass'));
        $userOne->setRoles(['ROLE_USER']);
        $userOne->setCustomer($customerOne);
        $manager->persist($userOne);

        $adminTwo = new User();
        $adminTwo->setUsername('jsanza');
        $adminTwo->setEmail('jeff@best-platform.fr');
        $adminTwo->setPassword($pwEncoder->encodePassword($adminOne, 'pass'));
        $adminTwo->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $adminTwo->setCustomer($customerTwo);
        $manager->persist($adminTwo);

        $userTwo = new User();
        $userTwo->setUsername('nflorio');
        $userTwo->setEmail('nathalie@best-platform.fr');
        $userTwo->setPassword($pwEncoder->encodePassword($adminOne, 'pass'));
        $userTwo->setRoles(['ROLE_USER']);
        $userTwo->setCustomer($customerTwo);
        $manager->persist($userTwo);

        $manager->flush();
    }
}
