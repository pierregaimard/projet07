<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture
{
    public const CUSTOMER_FLEXCON = 'Flexcon';
    public const CUSTOMER_BEST_PLATFORM = 'BestPlatform';

    public function load(ObjectManager $manager)
    {
        $customerOne = new Customer();
        $customerOne->setName('Flexcon Corp');
        $manager->persist($customerOne);

        $customerTwo = new Customer();
        $customerTwo->setName('My Best Platform');
        $manager->persist($customerTwo);

        $manager->flush();

        $this->addReference(self::CUSTOMER_FLEXCON, $customerOne);
        $this->addReference(self::CUSTOMER_BEST_PLATFORM, $customerTwo);
    }
}
