<?php

namespace App\Tests\Integration\Service;

use App\Service\ElasticsearchAdapter;
use App\Tests\Integration\Fixtures\ElasticsearchTestIndexManager;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ElasticsearchAdapterTest extends TestCase
{
    private ElasticsearchAdapter $adapter;
    private string $id = 'test_id';
    private array $body = ['foo' => 'bar'];
    private ElasticsearchTestIndexManager $indexManager;

    protected function setUp(): void
    {
        $address = sprintf(
            '%s:%s',
            getenv('ELASTICSEARCH_HOST'),
            getenv('ELASTICSEARCH_PORT')
        );

        $client = ClientBuilder::create()
            ->setHosts([$address])
            ->build();
        $this->adapter = new ElasticsearchAdapter($client);

        $this->indexManager = new ElasticsearchTestIndexManager($client);
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
