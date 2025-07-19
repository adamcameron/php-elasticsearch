<?php

namespace App\Tests\DataFixtures;

use App\Tests\Factory\CourseFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CourseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $conn = $manager->getConnection();
        $results = $conn->fetchAllAssociative('
            SELECT d.id AS department_id, i.id AS instructor_id
            FROM department d
            JOIN instructor i ON i.department_id = d.id
        ');

        $departmentInstructorMap = [];
        foreach ($results as $row) {
            $departmentId = $row['department_id'];
            $instructorId = $row['instructor_id'];
            $departmentInstructorMap[$departmentId][] = $instructorId;
        }

        CourseFactory::createBulkForDepartments($departmentInstructorMap, $manager);
        $manager->clear();
    }

    public function getDependencies(): array
    {
        return [
            InstructorFixtures::class,
        ];
    }
}
