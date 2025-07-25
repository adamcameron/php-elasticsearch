<?php

namespace App\MessageHandler;

use App\Message\LogMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LogMessageHandler
{
    public function __construct(
        private readonly LoggerInterface $messagingLogger,
    ) {
    }

    public function __invoke(LogMessage $message): void
    {
        $this->messagingLogger->log(
            $message->getLevel(),
            $message->getMessage(),
            $message->getContext()
        );
    }
}
