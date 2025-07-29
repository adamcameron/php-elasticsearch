<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;

#[AsCommand(
    name: 'app:long-lock',
    description: 'Acquires a long lock for testing lock contention.'
)]
class LongLockCommand extends Command
{
    public function __construct(
        private readonly LockFactory $lockFactory,
        private readonly LoggerInterface $lockingLogger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->lockingLogger->info('command_long_lock: started');

        $lock = $this->lockFactory->createLock('long_lock', 30);
        $this->lockingLogger->info('command_long_lock: lock created');

        if ($lock->acquire(true)) {
            $this->lockingLogger->info('command_long_lock: lock acquired');
            sleep(20); // Simulate a long-running process
            $this->lockingLogger->info('command_long_lock: processing done, releasing lock');
            $lock->release();
        } else {
            $this->lockingLogger->warning('command_long_lock: could not acquire lock');
        }

        $output->writeln('Lock operation completed.');
        return Command::SUCCESS;
    }
}
