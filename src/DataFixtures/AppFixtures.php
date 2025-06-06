<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * Flushes the changes made within the ObjectManager.
     *
     * @param ObjectManager $manager the ObjectManager instance to flush changes from
     */
    public function load(ObjectManager $manager): void
    {
        $manager->flush();
    }
}
