<?php

namespace App\Tests\Integration\Service;

use App\Service\ElasticsearchAdapter;
use App\Tests\Integration\Fixtures\ElasticsearchTestIndexManager;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ElasticsearchAdapterTest extends TestCase
{
    private ElasticsearchAdapter $adapter;
    private string $index = 'test_index';
    private string $id = 'test_id';
    private array $body = ['foo' => 'bar'];
    private Client $client;
    private ElasticsearchTestIndexManager $indexManager;

    protected function setUp(): void
    {
        $this->client = ClientBuilder::create()
            ->setHosts(['host.docker.internal:9200'])
            ->build();
        $this->adapter = new ElasticsearchAdapter($this->client);

        $this->indexManager = new ElasticsearchTestIndexManager($this->client);
        $this->indexManager->ensureIndexExists();
    }

    protected function tearDown(): void
    {
        $this->indexManager->removeIndexIfExists();
    }

    #[TestDox('Indexes a document successfully')]
    public function testIndexDocument(): void
    {
        $this->adapter->indexDocument($this->id, $this->body);
        $result = $this->adapter->getDocument($this->id);
        $this->assertEquals($this->body, $result);
    }

    #[TestDox('Retrieves a document successfully')]
    public function testGetDocument(): void
    {
        $this->adapter->indexDocument($this->id, $this->body);
        $result = $this->adapter->getDocument($this->id);
        $this->assertEquals($this->body, $result);
    }

    #[TestDox('Deletes a document successfully')]
    public function testDeleteDocument(): void
    {
        $this->adapter->indexDocument($this->id, $this->body);
        $this->adapter->deleteDocument($this->id);

        $this->expectException(ClientResponseException::class);
        $this->adapter->getDocument($this->id);
    }
}
