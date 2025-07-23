<?php

namespace App\Tests\Integration\System;

use App\Tests\Integration\Fixtures\ElasticsearchTestIndexManager;
use Elastic\Elasticsearch\Client;
use PHPUnit\Framework\TestCase;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearchTest extends TestCase
{
    private Client $client;
    private string $index = 'test_index';
    private string $id = 'test_id';
    private ElasticsearchTestIndexManager $indexManager;

    protected function setUp(): void
    {
        $this->client = ClientBuilder::create()
            ->setHosts(['host.docker.internal:9200'])
            ->build();

        $this->indexManager = new ElasticsearchTestIndexManager($this->client);
        $this->indexManager->ensureIndexExists($this->index);
    }

    protected function tearDown(): void
    {
        $this->indexManager->removeIndexIfExists($this->index);
    }

    public function testWriteAndReadDocument()
    {
        $doc = ['foo' => 'bar', 'baz' => 42];

        try {
            $this->client->index([
                'index' => $this->index,
                'id'    => $this->id,
                'body'  => $doc
            ]);

            $response = $this->client->get([
                'index' => $this->index,
                'id'    => $this->id
            ]);

            $this->assertEquals($doc, $response['_source']);
        } finally {
            $this->client->delete([
                'index' => $this->index,
                'id'    => $this->id
            ]);
        }
    }
}
