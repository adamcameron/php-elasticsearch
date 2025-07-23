<?php

namespace App\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use App\Entity\Instructor;
use App\Entity\Department;
use ReflectionClass;


class AbstractSyncableToElasticsearchTest extends TestCase
{
    #[TestDox('it populates the elasticsearch document with id, name, email, dept')]
    public function testElasticsearchDocumentIsPopulated()
    {
        $department = $this->createMock(Department::class);
        $department->method('getName')->willReturn('Physics');

        $instructor = new Instructor();
        $reflection = new ReflectionClass($instructor);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($instructor, 123);

        $instructor
            ->setFullName('Ada Lovelace')
            ->setEmail('ada@example.com')
            ->setDepartment($department);

        $doc = $instructor->toElasticsearchDocument();

        $this->assertEquals('instructor_123', $doc['id']);
        $this->assertEquals([
            'fullName' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'department' => 'Physics',
        ], $doc['body']);
    }
}
