<?php

namespace App\Service;

use Elastic\Elasticsearch\Client;

class ElasticsearchAdapter
{
    private const string INDEX = 'search_index';
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function indexDocument(string $id, array $body): void
    {
        $this->client->index([
            'index' => self::INDEX,
            'id'    => $id,
            'body'  => $body,
        ]);
    }

    public function getDocument(string $id): array
    {
        $response = $this->client->get([
            'index' => self::INDEX,
            'id'    => $id,
        ]);
        return $response['_source'] ?? [];
    }

    public function deleteDocument(string $id): void
    {
        $this->client->delete([
            'index' => self::INDEX,
            'id'    => $id,
        ]);
    }

    public function searchByString(string $query): array
    {
        $body = [
            'query' => [
                'query_string' => [
                    'query' => $query,
                ],
            ],
        ];
        $response = $this->client->search([
            'index' => self::INDEX,
            'body'  => $body,
        ]);
        return $response['hits']['hits'] ?? [];
    }
}
