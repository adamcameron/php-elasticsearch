<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:log-test', description: 'Logs a test message.')]
class LogTestCommand extends Command
{
    public function __construct(private readonly LoggerInterface $messagingLogger)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Logs a test message.')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message to log', 'Default command log message');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = $input->getArgument('message');
        $this->messagingLogger->info($message);
        $output->writeln($message);

        return Command::SUCCESS;
    }
}
