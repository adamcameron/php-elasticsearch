<?php

namespace App\EventListener;

use App\Entity\SyncableToElasticsearch;
use App\Service\ElasticsearchAdapter;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postUpdate, priority: 500, connection: 'default')]
class SearchIndexer
{
    public function __construct(
        private readonly ElasticsearchAdapter $adapter,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {}

    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->sync($args->getObject());
    }

    public function postUpdate(PostUpdateEventArgs  $args): void
    {
        $this->sync($args->getObject());
    }

    public function sync(object $entity): void
    {
        if (!$entity instanceof SyncableToElasticsearch) {
            return;
        }

        $doc = $entity->toElasticsearchDocument();
        $doc['body']['_meta'] = [
            'type' => $entity->getShortName(),
            'title' => $entity->getSearchTitle(),
            'url' => $this->urlGenerator->generate(
                $entity->getShortName() . '_view',
                ['id' => $entity->getId()]
            ),
        ];

        $this->adapter->indexDocument($doc['id'], $doc['body']);
    }
}
