<?php

declare(strict_types=1);

namespace Confetti\Helpers;

use IteratorAggregate;
use Traversable;

class ContentStore
{
    private QueryBuilder $queryBuilder;
    private array $content = [];
    // When null, we have not cached the content yet
    private ?array $contentCurrentLevel = null;
    private bool $alreadyInit = false;

    public function __construct(string $from, string $as)
    {
        $this->queryBuilder = new QueryBuilder($from, $as);
    }

    /**
     * @param string|null $firstAs The first as to use to determine the level.
     *                             if null, then we are at the top level.
     */
    public function init(?string $firstAs = null): void
    {
        if ($this->alreadyInit) {
            return;
        }
        echo "Initializing\n";
        $this->queryBuilder->setOptions([
            'use_cache' => true,
        ]);
        $this->content             = $this->queryBuilder->run()[0] ?? ['join' => []];
        $this->contentCurrentLevel = $firstAs ? $this->content['join'][$firstAs] : $this->content;
        $this->alreadyInit         = true;
    }

    public function getCurrentLevelCachedData(): ?array
    {
        return $this->contentCurrentLevel;
    }

    public function setCurrentLevelCachedData(array $data): void
    {
        $this->contentCurrentLevel = $data;
    }

    public function join(string $from, string $as): void
    {
        $joins = $this->contentCurrentLevel['join'] ?? [];
        $this->contentCurrentLevel = $joins[$as] ?? null;
        $this->queryBuilder->wrapJoin($from, $as);
    }

    public function findOneData(string $id): mixed
    {
        // Ensure that the content is initialized
        $this->init();
        // Check if content is present
        // If key is not present, then the query is never cached before
        if (array_key_exists('data', $this->contentCurrentLevel) && array_key_exists($id, $this->contentCurrentLevel["data"])) {
            return $this->contentCurrentLevel["data"][$id];
        }
        // Get the content and cache the selection
        $this->queryBuilder->setOptions([
            'patch_cache_select' => true,
            'only_first'         => true,
            'use_cache'          => false,
        ]);
        $this->queryBuilder->setSelect([$id]);
        $result = $this->queryBuilder->run();
        if (count($result) === 0) {
            return null;
        }
        return $this->searchSelectedData($result) ?? null;
    }

    // This is to prevent n+1 problems. We need to load the
    // first item. And then later (in another function) we
    // load the rest of the items in one go.
    public function findFirstOfJoin(): ?array
    {
        // Ensure that the content is initialized
        if (!$this->alreadyInit) {
            $this->init();
        }
        $child = $this->queryBuilder;
        // Get the content and cache the selection
        $child->setOptions([
            'patch_cache_join' => true,
            'only_first'       => true,
            'use_cache'        => false,
        ]);
        /// so we can use where and so forth
        $result = $child->run();
        if (count($result) === 0) {
            return null;
        }
        return $this->searchJoin($result);
    }

    // After findFirstOfJoin we can use this function
    // to load the rest of the items with all cached queries
    public function findRestOfJoin(): ?array
    {
        $child = $this->queryBuilder;
        $child->setOptions([
            'use_cache'            => true,
            'use_cache_only_joins' => true,
        ]);
        $limit = $child->getLimit();
        if ($limit !== null) {
            $child->setLimit($limit - 1);
        }
        $offset = $child->getOffset();
        $child->setOffset($offset + 1);
        return $child->run();
    }

    /**
     * Search for the selected data in the result array recursively.
     * We assume that there is only one selected data.
     */
    private function searchSelectedData(array $items): mixed
    {
        $item = array_pop($items);
        if (empty($item['data']) && !empty($item['join'])) {
            // Get first join from the array with string keys
            $join = array_pop($item['join']);
            return $this->searchSelectedData($join);

        }
        return array_pop($item['data']);
    }

    /**
     * Search for the join in the result array recursively.
     * We assume that there is only one join.
     */
    private function searchJoin(array $items): mixed
    {
        $item = array_pop($items);
        if (!empty($item['join'])) {
            // Get first join from the array with string keys
            $join = array_pop($item['join']);
            return $this->searchJoin($join);

        }
        return $item;
    }
}
