<?php

namespace App\Tests\DataFixtures;

use App\Entity\Course;
use App\Tests\Factory\AssignmentFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AssignmentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $courseIds = $manager
            ->getRepository(Course::class)
            ->createQueryBuilder('c')
            ->select('c.id')
            ->getQuery()
            ->getSingleColumnResult();

        AssignmentFactory::createForCourses($courseIds, $manager);
        $manager->clear();
    }

    public function getDependencies(): array
    {
        return [
            CourseFixtures::class,
        ];
    }
}
