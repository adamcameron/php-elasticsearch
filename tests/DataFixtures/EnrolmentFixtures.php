<?php

namespace App\Tests\DataFixtures;

use App\Entity\Student;
use App\Entity\Course;
use App\Tests\Factory\EnrolmentFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EnrolmentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $conn = $manager->getConnection();
        $sql = <<<SQL
            INSERT INTO enrolment (student_id, course_id)
            SELECT student_id, course_id
            FROM (
                SELECT
                    s.id AS student_id,
                    c.id AS course_id,
                    ROW_NUMBER() OVER (PARTITION BY s.id ORDER BY RAND()) AS rn
                FROM student s
                INNER JOIN department d ON s.department_id = d.id
                INNER JOIN course c ON c.department_id = d.id
            ) ranked
            WHERE ranked.rn BETWEEN 1 AND 4
        SQL;
        $conn->executeStatement($sql);
    }

    public function getDependencies(): array
    {
        return [
            StudentFixtures::class,
            CourseFixtures::class,
        ];
    }
}
