<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/lock', name: 'app_lock')]
class LockController extends AbstractController
{
    public function __construct(
        private readonly LockFactory $lockFactory,
        private readonly LoggerInterface $lockingLogger
    )
    {
    }

    #[Route('/long', name: 'app_lock_long')]
    public function longLock(): Response
    {
        $this->lockingLogger->info('app_lock_long: started');

        $lock = $this->lockFactory->createLock('long_lock', 30);
        $this->lockingLogger->info('app_lock_long: lock created');

        if ($lock->acquire(true)) {
            $this->lockingLogger->info('app_lock_long: lock acquired');
            sleep(20); // Simulate a long-running process
            $this->lockingLogger->info('app_lock_long: processing done, releasing lock');
            $lock->release();
        } else {
            $this->lockingLogger->warning('app_lock_long: could not acquire lock');
        }

        return new Response('Lock operation completed.');
    }
}
