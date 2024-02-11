<?php

declare(strict_types=1);

namespace Confetti\Helpers;

use IteratorAggregate;
use Traversable;

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
        $this->content     = $this->queryBuilder->get()[0] ?? [];
        $this->alreadyInit = true;
    }

    public function join(string $from, string $as): void
    {
        $this->queryBuilder->join($from, $as);
    }

    public function find(string $id): mixed
    {
        // Ensure that the content is initialized
        $this->init();
        // Check if content is present
        // If key is not present, then the query is never cached before
        if (array_key_exists('data', $this->content) && array_key_exists($id, $this->content["data"])) {
            return $this->content["data"][$id];
        }
        // Get the content and cache the selection
        $this->queryBuilder->setOptions([
            'patch_cache_select' => true,
            'only_first'         => true,
        ]);
        $this->queryBuilder->setSelect([$id]);
        $result = $this->queryBuilder->get();
        if (count($result) === 0) {
            return null;
        }
        return $result[0]['data'][$id] ?? null;
    }

    // This is to prevent n+1 problems. We need to load the
    // first item. And then later (in another function) we
    // load the rest of the items in one go.
    public function findFirstOfJoin(string $from): ?array
    {
        // Ensure that the content is initialized
        if (!$this->alreadyInit) {
            $this->init();
        }
        // Get the content and cache the selection
        $this->queryBuilder->setOptions([
            'patch_cache_join' => true,
            'only_first'       => true,
            'use_cache'        => false,
        ]);
        /// so we can use where and so forth
        $result = $this->queryBuilder->get();
        if (count($result) === 0) {
            return null;
        }
        return $result[0];
    }
}
