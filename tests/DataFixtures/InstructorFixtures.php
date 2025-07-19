<?php

namespace App\Tests\DataFixtures;

use App\Entity\Department;
use App\Tests\Factory\InstructorFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InstructorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $departmentIds = $manager->getRepository(Department::class)
            ->createQueryBuilder('d')
            ->select('d.id')
            ->getQuery()
            ->getSingleColumnResult();

        InstructorFactory::createForDepartments($departmentIds, $manager);
        $manager->clear();
    }

    public function getDependencies(): array
    {
        return [
            DepartmentFixtures::class,
        ];
    }
}
