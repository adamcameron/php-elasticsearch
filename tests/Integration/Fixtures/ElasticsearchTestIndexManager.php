<?php

namespace App\Tests\Integration\Fixtures;

use Elastic\Elasticsearch\Client;

class ElasticsearchTestIndexManager
{
    private const string INDEX = 'search_index';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function ensureIndexExists(): void
    {
        if (!$this->client->indices()->exists(['index' => self::INDEX])->asBool()) {
            $this->client->indices()->create(['index' => self::INDEX]);
        }
    }

    public function removeIndexIfExists(): void
    {
        if ($this->client->indices()->exists(['index' => self::INDEX])->asBool()) {
            $this->client->indices()->delete(['index' => self::INDEX]);
        }
    }
}
