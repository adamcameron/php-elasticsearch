<?php

namespace App\Tests\Factory;

use App\Entity\Department;
use App\Entity\Institution;
use App\Tests\Fixtures\Department as DepartmentEnum;
use App\Tests\Fixtures\FixtureLimits;
use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class DepartmentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Department::class;
    }

    protected function initialize(): static
    {
        return $this;
    }

    protected function defaults(): array|callable
    {
        $institution = InstitutionFactory::random();
        return [
            'name' => self::randomDepartmentNameForInstitutionUnique() . ' Department',
            'headOfDepartment' => self::faker()->name(),
            'building' => self::faker()->buildingNumber(),
            'contactEmail' => self::faker()->companyEmail(),
            'institution' => $institution,
        ];
    }

    public static function createBulkForInstitutions(array $institutionIds, ObjectManager $manager): void
    {
        $values = [];
        $params = [];
        foreach ($institutionIds as $id) {
            $count = rand(FixtureLimits::DEPARTMENTS_MIN, FixtureLimits::DEPARTMENTS_MAX);
            $usedNames = [];
            for ($i = 0; $i < $count; $i++) {
                $name = self::randomDepartmentNameForInstitutionUnique($usedNames);
                $usedNames[] = $name;
                $defaults = (new self())->defaults();
                $values[] = '(?, ?, ?, ?, ?)';
                $params[] = $name . ' Department';
                $params[] = $defaults['headOfDepartment'];
                $params[] = $defaults['building'];
                $params[] = $defaults['contactEmail'];
                $params[] = $id;
            }
        }
        if ($values) {
            $sql = 'INSERT INTO department (name, head_of_department, building, contact_email, institution_id) VALUES ' . implode(', ', $values);
            $manager->getConnection()->executeStatement($sql, $params);
        }
    }

    private static function randomDepartmentNameForInstitutionUnique(array $usedNames = []): string
    {
        $allNames = array_map(fn($enum) => $enum->value, DepartmentEnum::cases());
        $available = array_diff($allNames, $usedNames);

        if (empty($available)) {
            throw new \RuntimeException('No unused department names left for this institution.');
        }

        return self::faker()->randomElement($available);
    }
}
