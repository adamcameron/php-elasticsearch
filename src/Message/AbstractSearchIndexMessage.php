<?php

namespace App\Message;

use Doctrine\Persistence\Event\LifecycleEventArgs;

abstract class AbstractSearchIndexMessage
{
    public function __construct(
        private readonly object $args
    )
    { }

    public function getArgs(): object
    {
        return $this->args;
    }
}
