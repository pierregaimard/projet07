<?php

namespace App\Tests;

use App\Entity\Product;

abstract class AbstractProductTestCase extends AbstractAppTestCase
{
    private const NAME        = 'name';
    private const DESCRIPTION = 'description';
    private const COLORS      = 'colors';
    private const PRICE       = 'price';

    protected function loadProductsFixtures()
    {
        $manager = $this->getEntityManager();
        $mobiles = [
            [
                self::NAME => 'BileMo X4-LG20 Test',
                self::DESCRIPTION => 'This is an amazing smartphone !',
                self::PRICE => 299,
                self::COLORS => [
                    'Red', 'Blue', 'Black', 'White', 'Grey'
                ]
            ],
            [
                self::NAME => 'BileMo S40 TX20 Test',
                self::DESCRIPTION => 'This is another amazing big smartphone !',
                self::PRICE => 399,
                self::COLORS => [
                    'Red', 'Blue', 'White', 'Grey', 'Black'
                ]
            ],
            [
                self::NAME => 'BileMo S80 TX30 Black Test',
                self::DESCRIPTION => 'This amazing smartphone is unbreakable !',
                self::PRICE => 489,
                self::COLORS => [
                    'Red', 'Blue', 'White', 'Grey', 'Pink', 'Yellow'
                ]
            ],
            [
                self::NAME => 'BileMo S200 TX50 Black Test',
                self::DESCRIPTION => 'With this phone, you can call your friends from the moon !!',
                self::PRICE => 789,
                self::COLORS => [
                    'Blue', 'Black', 'Grey', 'Pink'
                ]
            ],
            [
                self::NAME => 'BileMo S400 VX50 LT Black Test',
                self::DESCRIPTION => 'This amazing phone has been built for the next generations !!',
                self::PRICE => 1249,
                self::COLORS => [
                    'Blue', 'Black', 'Grey', 'Pink', 'Red'
                ]
            ],
        ];

        $mobileCount = 0;
        foreach ($mobiles as $mobile) {
            foreach ($mobile[self::COLORS] as $color) {
                $mobileCount ++;
                $product  = 'mobile' . $mobileCount;
                $$product = new Product();
                $$product->setName(sprintf('%s %s', $mobile[self::NAME], $color));
                $$product->setDescription($mobile[self::DESCRIPTION]);
                $$product->setColor($color);
                $$product->setPrice($mobile[self::PRICE]);
                $manager->persist($$product);
            }
        }

        $manager->flush();
    }
}
