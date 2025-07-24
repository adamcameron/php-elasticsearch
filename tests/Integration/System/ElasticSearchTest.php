<?php

namespace App\Tests\Integration\System;

use App\Tests\Integration\Fixtures\ElasticsearchTestIndexManager;
use Elastic\Elasticsearch\Client;
use PHPUnit\Framework\TestCase;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearchTest extends TestCase
{
    private Client $client;
    private string $id = 'test_id';
    private ElasticsearchTestIndexManager $indexManager;

    private const string INDEX = ElasticsearchTestIndexManager::INDEX;

    protected function setUp(): void
    {
        $address = sprintf(
            '%s:%s',
            getenv('ELASTICSEARCH_HOST'),
            getenv('ELASTICSEARCH_PORT')
        );

        $this->client = ClientBuilder::create()
            ->setHosts([$address])
            ->build();

        $this->indexManager = new ElasticsearchTestIndexManager($this->client);
        $this->indexManager->ensureIndexExists();
    }

    protected function tearDown(): void
    {
        $this->indexManager->removeIndexIfExists();
    }

    public function testWriteAndReadDocument()
    {
        $doc = ['foo' => 'bar', 'baz' => 42];

        try {
            $this->client->index([
                'index' => self::INDEX,
                'id'    => $this->id,
                'body'  => $doc
            ]);

            $response = $this->client->get([
                'index' => self::INDEX,
                'id'    => $this->id
            ]);

            $this->assertEquals($doc, $response['_source']);
        } finally {
            $this->client->delete([
                'index' => self::INDEX,
                'id'    => $this->id
            ]);
        }
    }
}
