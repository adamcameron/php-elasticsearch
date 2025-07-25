<?php

namespace App\Message;

use Doctrine\Persistence\Event\LifecycleEventArgs;

abstract class AbstractSearchIndexMessage
{
    public function __construct(
        private readonly LifecycleEventArgs $args
    )
    { }

    public function getArgs(): LifecycleEventArgs
    {
        return $this->args;
    }
}
