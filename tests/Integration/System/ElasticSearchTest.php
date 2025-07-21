<?php
use PHPUnit\Framework\TestCase;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearchTest extends TestCase
{
    private $client;
    private $index = 'test_index';
    private $id = 'test_id';

    protected function setUp(): void
    {
        $this->client = ClientBuilder::create()
            ->setHosts(['host.docker.internal:9200'])
            ->build();
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
