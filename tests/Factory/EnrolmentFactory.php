<?php

namespace App\Tests\Factory;

use App\Entity\Enrolment;
use App\Entity\Student;
use App\Entity\Course;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class EnrolmentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Enrolment::class;
    }

    protected function initialize(): static
    {
        return $this;
    }

    protected function defaults(): array|callable
    {
        return [
            // student and course set in fixture
        ];
    }

    public static function createForStudentAndCourse(Student $student, Course $course): void
    {
        self::new([
            'student' => $student,
            'course' => $course,
        ])->create();
    }
}
