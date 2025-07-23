<?php

namespace App\Entity;

abstract class AbstractSyncableToElasticsearch implements SyncableToElasticsearch
{
    protected ?int $id;

    abstract public function toElasticsearchArray(): array;

    public function toElasticsearchDocument(): array
    {
        return [
            'id' => $this->getElasticsearchId(),
            'body' => $this->toElasticsearchArray(),
        ];
    }

    protected function getElasticsearchId(): string
    {
        $entityType = $this->getShortName();
        return $entityType . '_' . $this->id;
    }

    public function getShortName(): string
    {
        $parts = explode('\\', static::class);
        $entityType = strtolower(end($parts));

        return $entityType;
    }
}
