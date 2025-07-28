<?php

namespace App\EventListener;

use App\Message\SearchIndexAddMessage;
use App\Message\SearchIndexDeleteMessage;
use App\Message\SearchIndexUpdateMessage;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postUpdate, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::preRemove, priority: 500, connection: 'default')]
class SearchIndexer
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ) {}

    public function postPersist(PostPersistEventArgs $args): void
    {
        $indexMessage = new SearchIndexAddMessage($args->getObject());
        $this->bus->dispatch($indexMessage);
    }

    public function postUpdate(PostUpdateEventArgs  $args): void
    {
        $indexMessage = new SearchIndexUpdateMessage($args->getObject());
        $this->bus->dispatch($indexMessage);
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $indexMessage = new SearchIndexDeleteMessage($args->getObject());
        $this->bus->dispatch($indexMessage);
    }
}
