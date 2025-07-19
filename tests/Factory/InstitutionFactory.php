<?php

namespace App\Tests\Factory;

use App\Entity\Institution;
use App\Enum\InstitutionType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class InstitutionFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Institution::class;
    }

    protected function initialize(): static
    {
        return $this;
    }

    protected function defaults(): array|callable
    {
        $city = self::faker()->city();
        $type = self::faker()->randomElement(InstitutionType::cases());
        $typeLabel = $type->label();

        return [
            'name' => "{$city} {$typeLabel}",
            'address' => self::faker()->address(),
            'city' => $city,
            'postal_code' => self::faker()->postcode(),
            'country' => self::faker()->country(),
            'established_year' => self::faker()->year(),
            'type' => $type,
            'website' => 'https://example.com/' . $this->faker()->slug(),
        ];
    }
}
