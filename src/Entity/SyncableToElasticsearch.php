<?php

namespace App\Entity;

interface SyncableToElasticsearch
{
    public function toElasticsearchDocument(): array;
    public function getSearchTitle(): string;
}
