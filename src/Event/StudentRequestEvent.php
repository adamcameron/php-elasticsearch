<?php

namespace App\Event;

use App\Entity\Student;
use Symfony\Component\HttpFoundation\Request;

class StudentRequestEvent
{
    public function __construct(
        private readonly Request $request,
        private readonly Student $student
    ) { }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getStudent(): Student
    {
        return $this->student;
    }
}
