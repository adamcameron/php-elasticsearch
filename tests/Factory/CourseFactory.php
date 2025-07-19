<?php

namespace App\Tests\Factory;

use App\Entity\Course;
use App\Entity\Department;
use App\Tests\Fixtures\Department as DepartmentEnum;
use App\Tests\Fixtures\FixtureLimits;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CourseFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Course::class;
    }

    protected function initialize(): static
    {
        return $this;
    }

    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(3), // fallback if not set
            'code' => strtoupper(self::faker()->bothify('???###')),
            'description' => self::faker()->paragraph(),
            // department and instructor set in fixtures
        ];
    }

    public static function createBulkForDepartments(array $departmentInstructorMap, ObjectManager $manager): void
    {
        $values = [];
        $params = [];
        foreach ($departmentInstructorMap as $departmentId => $instructorIds) {
            if (empty($instructorIds)) {
                continue;
            }
            $departmentRef = $manager->getReference(\App\Entity\Department::class, $departmentId);
            $titles = self::getCourseTitlesForDepartment($departmentRef);
            $count = random_int(FixtureLimits::COURSES_MIN, FixtureLimits::COURSES_MAX);
            for ($i = 0; $i < $count; $i++) {
                $defaults = (new self())->defaults();
                $title = self::faker()->randomElement($titles) ?? $defaults['title'];
                $instructorId = self::faker()->randomElement($instructorIds);
                $values[] = '(?, ?, ?, ?, ?)';
                $params[] = $title;
                $params[] = $defaults['code'];
                $params[] = $defaults['description'];
                $params[] = $departmentId;
                $params[] = $instructorId;
            }
        }
        if ($values) {
            $sql = 'INSERT INTO course (title, code, description, department_id, instructor_id) VALUES ' . implode(', ', $values);
            $manager->getConnection()->executeStatement($sql, $params);
        }
    }

    private static function getCourseTitlesForDepartment(\App\Entity\Department $department): array
    {
        $enumName = str_replace(' Department', '', $department->getName());
        foreach (DepartmentEnum::cases() as $case) {
            if ($case->value === $enumName) {
                return $case->courseTitles();
            }
        }
        return [];
    }
}
