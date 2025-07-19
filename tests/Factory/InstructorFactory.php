<?php

namespace App\Tests\Factory;

use App\Entity\Instructor;
use App\Tests\Fixtures\FixtureLimits;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class InstructorFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Instructor::class;
    }

    protected function initialize(): static
    {
        return $this;
    }

    protected function defaults(): array|callable
    {
        $fullName = self::faker()->name();
        $localPart = strtolower(str_replace(' ', '.', $fullName));
        $safeEmail = self::faker()->safeEmail();
        $domain = substr($safeEmail, strpos($safeEmail, '@') + 1);
        $email = $localPart . '@' . $domain;

        return [
            'fullName' => $fullName,
            'email' => $email,
            'department' => DepartmentFactory::random(),
        ];
    }

    public static function createForDepartments(array $departmentIds, ObjectManager $manager): void
    {
        $values = [];
        $params = [];
        foreach ($departmentIds as $id) {
            $count = self::faker()->numberBetween(
                FixtureLimits::INSTRUCTORS_MIN,
                FixtureLimits::INSTRUCTORS_MAX
            );
            for ($i = 0; $i < $count; $i++) {
                $defaults = (new self())->defaults();
                $values[] = '(?, ?, ?)';
                $params[] = $defaults['fullName'];
                $params[] = $defaults['email'];
                $params[] = $id;
            }
        }

        if ($values) {
            $sql = 'INSERT INTO instructor (full_name, email, department_id) VALUES ' . implode(', ', $values);
            $manager->getConnection()->executeStatement($sql, $params);
        }
    }
}
