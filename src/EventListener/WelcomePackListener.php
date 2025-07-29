<?php

namespace App\EventListener;

use App\Event\StudentRequestEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class WelcomePackListener
{
    public function __construct(
        private readonly LoggerInterface $eventsLogger
    )
    { }

    #[AsEventListener()]
    public function sendStudentWelcomePack(StudentRequestEvent $event): void
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if ($route !== 'student_add') {
            $this->eventsLogger->notice("sendStudentWelcomePack skipped for route [$route]");
            return;
        }
        $student = $event->getStudent();
        $this->eventsLogger->info(
            sprintf('Send Welcome Pack to %s', $student->getFullName()),
            ['student' => $student]
        );
    }
}
