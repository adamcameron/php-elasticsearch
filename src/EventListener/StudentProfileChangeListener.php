<?php

namespace App\EventListener;

use App\Event\StudentRequestEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class StudentProfileChangeListener
{
    public function __construct(
        private readonly LoggerInterface $eventsLogger
    )
    { }

    #[AsEventListener()]
    public function validateProfileChange(StudentRequestEvent $event): void
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if (!in_array($route, ['student_add', 'student_edit'])) {
            $this->eventsLogger->notice("validateProfileChange skipped for route [$route]");
            return;
        }
        $student = $event->getStudent();
        $this->eventsLogger->info(
            sprintf('Validate profile change: %s', $student->getFullName()),
            ['student' => $student]
        );
    }
}
