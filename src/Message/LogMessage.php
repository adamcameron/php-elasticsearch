<?php

namespace App\Message;

use Monolog\Level;

class LogMessage
{
    public function __construct(
        private readonly string $message,
        private readonly Level $level = Level::Info,
        private readonly array $context = []
    )
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLevel(): Level
    {
        return $this->level;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
