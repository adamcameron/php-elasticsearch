<?php

namespace App\Service;

use App\Entity\SyncableToElasticsearch;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ElasticSearchIndexerService
{
    public function __construct(
        private readonly ElasticsearchAdapter $adapter,
        private readonly UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    public function sync(object $object): void
    {
        if (!$object instanceof SyncableToElasticsearch) {
            return;
        }

        $doc = $object->toElasticsearchDocument();
        $doc['body']['_meta'] = [
            'type' => $object->getShortName(),
            'title' => $object->getSearchTitle(),
            'url' => $this->urlGenerator->generate(
                $object->getShortName() . '_view',
                ['id' => $object->getId()]
            ),
        ];

        $this->adapter->indexDocument($doc['id'], $doc['body']);
    }

    public function delete(object $object): void
    {
        if (!$object instanceof SyncableToElasticsearch) {
            return;
        }

        $this->adapter->deleteDocument($object->getElasticsearchId());
    }
}
