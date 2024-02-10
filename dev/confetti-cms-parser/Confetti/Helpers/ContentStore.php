<?php

declare(strict_types=1);

namespace Confetti\Helpers;

class ContentStore
{
    private QueryBuilder $queryBuilder;
    private array $content = [];
    private bool $alreadyInit = false;

    public function __construct(string $from, string $as)
    {
        $this->queryBuilder = new QueryBuilder($from, $as);
    }

    public function init(): void
    {
        if ($this->alreadyInit) {
            return;
        }
        $this->queryBuilder->setOptions([
            'use_cache' => true,
        ]);
        $this->content = $this->queryBuilder->get()[0] ?? [];
        $this->alreadyInit = true;
    }

    public function find(string $id): mixed
    {
        // Ensure that the content is initialized
        if (!$this->alreadyInit) {
            $this->init();
        }
        // Check if content is present
        if (array_key_exists($id, $this->content["data"]) && $this->content["data"][$id] !== null) {
            return $this->content["data"][$id];
        }
        // Get the content and cache the selection
        $this->queryBuilder->setOptions([
            'patch_cache_select' => true,
            'only_first' => true,
        ]);
        $this->queryBuilder->setSelect([$id]);
        $result = $this->queryBuilder->get();
        if (count($result) === 0) {
            return null;
        }
        return $result[0]['data'][$id] ?? null;
    }

    public function findMany(string $id): array
    {
        exit('todo');
        $this->content = $this->queryBuilder->get();
        return $this->content["data"];
    }
}
