<?php

namespace App\Tests\Factory;

use App\Entity\Student;
use App\Enum\StudentStatus;
use App\Tests\Fixtures\FixtureLimits;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Doctrine\Persistence\ObjectManager;

class StudentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Student::class;
    }

    protected function initialize(): static
    {
        return $this;
    }

    protected function defaults(): array|callable
    {
        return [
            'fullName' => self::faker()->name(),
            'email' => self::faker()->unique()->safeEmail(),
            'dateOfBirth' => self::faker()->dateTimeBetween('-30 years', '-18 years'),
            'gender' => self::faker()->randomElement(['male', 'female', null]),
            'enrolmentYear' => self::faker()->numberBetween(2015, 2024),
            'status' => self::faker()->randomElement(StudentStatus::cases()),
            // department set in fixture
        ];
    }

    public static function createForDepartments(array $departmentIds, ObjectManager $manager): void
    {
        $values = [];
        $params = [];
        foreach ($departmentIds as $id) {
            $count = random_int(FixtureLimits::STUDENTS_MIN, FixtureLimits::STUDENTS_MAX);
            for ($i = 0; $i < $count; $i++) {
                $defaults = (new self())->defaults();
                $values[] = '(?, ?, ?, ?, ?, ?, ?)';
                $params[] = $defaults['fullName'];
                $params[] = $defaults['email'];
                $params[] = $defaults['dateOfBirth']->format('Y-m-d');
                $params[] = $defaults['gender'];
                $params[] = $defaults['enrolmentYear'];
                $params[] = $defaults['status']->value;
                $params[] = $id;
            }
        }

        if ($values) {
            $sql = 'INSERT INTO student (full_name, email, date_of_birth, gender, enrolment_year, status, department_id) VALUES ' . implode(', ', $values);
            $manager->getConnection()->executeStatement($sql, $params);
        }
    }
}
