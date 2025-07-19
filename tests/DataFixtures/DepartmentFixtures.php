<?php

namespace App\Tests\DataFixtures;

use App\Entity\Institution;
use App\Tests\Factory\DepartmentFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DepartmentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $institutionIds = $manager
            ->getRepository(Institution::class)
            ->createQueryBuilder('i')
            ->select('i.id')
            ->getQuery()
            ->getSingleColumnResult();

        DepartmentFactory::createBulkForInstitutions($institutionIds, $manager);
        $manager->clear();
    }

    public function getDependencies(): array
    {
        return [
            InstitutionFixtures::class,
        ];
    }
}
