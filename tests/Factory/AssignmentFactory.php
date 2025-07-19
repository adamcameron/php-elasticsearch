<?php

namespace App\Tests\Factory;

use App\Entity\Assignment;
use App\Entity\Course;
use App\Tests\Fixtures\FixtureLimits;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class AssignmentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Assignment::class;
    }

    protected function initialize(): static
    {
        return $this;
    }

    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(4),
            'description' => self::faker()->paragraph(),
            // course set in fixture
        ];
    }

    public static function createForCourses(array $courseIds, ObjectManager $manager): void
    {
        $values = [];
        $params = [];
        foreach ($courseIds as $id) {
            $count = random_int(FixtureLimits::ASSIGNMENTS_MIN, FixtureLimits::ASSIGNMENTS_MAX);
            for ($i = 0; $i < $count; $i++) {
                $defaults = (new self())->defaults();
                $values[] = '(?, ?, ?)';
                $params[] = $defaults['title'];
                $params[] = $defaults['description'];
                $params[] = $id;
            }
        }

        if ($values) {
            $sql = 'INSERT INTO assignment (title, description, course_id) VALUES ' . implode(', ', $values);
            $manager->getConnection()->executeStatement($sql, $params);
        }
    }
}
