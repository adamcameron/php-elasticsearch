<?php

namespace App\MessageHandler;

use App\Message\SearchIndexAddMessage;
use App\Message\SearchIndexDeleteMessage;
use App\Message\SearchIndexUpdateMessage;
use App\Service\ElasticSearchIndexerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class SearchIndexMessageHandler
{
    public function __construct(
        private readonly ElasticSearchIndexerService $searchIndexer,
    ) {
    }

    #[AsMessageHandler]
    public function handleAdd(SearchIndexAddMessage $message): void
    {
        $this->searchIndexer->sync($message->getArgs());
    }

    #[AsMessageHandler]
    public function handleUpdate(SearchIndexUpdateMessage $message): void
    {
        $this->searchIndexer->sync($message->getArgs());
    }

    #[AsMessageHandler]
    public function handleRemove(SearchIndexDeleteMessage $message): void
    {
        $this->searchIndexer->delete($message->getArgs());
    }
}
