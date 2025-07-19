<?php

namespace App\Tests\DataFixtures;

use App\Tests\Factory\InstitutionFactory;
use App\Tests\Fixtures\FixtureLimits;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class InstitutionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        InstitutionFactory::createMany(FixtureLimits::INSTITUTIONS_MAX);
        $manager->flush();
    }
}
