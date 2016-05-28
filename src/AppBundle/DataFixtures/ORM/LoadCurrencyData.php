<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Currency;

class LoadCurrencyData implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $currencies = array(
            array(
                'code' => 'USD',
                'title' => '1 доллар США'
            ),
            array(
                'code' => 'EUR',
                'title' => '1 евро'
            ),
            array(
                'code' => 'RUB',
                'title' => '1 российский рубль'
            ),
            array(
                'code' => 'GBP',
                'title' => '1 фунт стерлингов'
            )
        );
        foreach ($currencies as $currency) {
            $item = new Currency();
            $item->setCode($currency['code']);
            $item->setTitle($currency['title']);
            $manager->persist($item);
        }

        $manager->flush();
    }
}