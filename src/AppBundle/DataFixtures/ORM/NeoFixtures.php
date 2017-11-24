<?php

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Neo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class NeoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $neo = new Neo();
            $neo->setReference(rand(1000000, 9999999));
            $neo->setDate(new \DateTime('NOW'));
            $neo->setName("neo name {$i}");
            $neo->setSpeed(rand(100000000000, 999999999999999) / 1000000000);
            $neo->setIsHazardous(rand(0,1));

            $manager->persist($neo);
        }

        $manager->flush();
    }

}