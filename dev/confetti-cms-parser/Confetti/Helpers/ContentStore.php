<?php

declare(strict_types=1);

namespace Confetti\Helpers;

class ContentStore
{
    private QueryBuilder $queryBuilder;
    private array $content = [];
    private bool $alreadyInit = false;
    // This is a fake store, used for mocking
    // data for development No database queries are made
    private bool $isFake = false;

    /**
     * @var array array with 'type' and 'path'
     */
    private array $breadcrumbs = [];

    public function __construct(string $from, string $as)
    {
        $this->breadcrumbs[] = ['type' => 'id', 'path' => $from];
        $this->queryBuilder  = new QueryBuilder($from, $as);
    }

    public function init(): void
    {
        if ($this->alreadyInit) {
            return;
        }
        $this->queryBuilder->setOptions([
            'use_cache'          => true,
            'patch_cache_select' => true,
        ]);
        $this->content     = $this->queryBuilder->run($this->isFake)[0] ?? [];
        $this->alreadyInit = true;
    }

    public function isFake(): bool
    {
        return $this->isFake;
    }

    public function setIsFake(): void
    {
        $this->isFake = true;
    }

    public function appendCurrentJoin(string $relativeId): void
    {
        $this->breadcrumbs[] = ['type' => 'id', 'path' => $relativeId];
        // The item in a list is first abstract `/model/item~`, so we can fetch
        // all the children when we loop over the children, we want to replace
        // de abstract "from" with the specific id `/model/item~1y63jg9kej`.
        // That way we can fetch new data when data is missing. This is
        // important because we want to get only new data from this item
        $this->queryBuilder->replaceFrom($relativeId);
    }

    public function join(string $from, string $as): void
    {
        $this->breadcrumbs[] = ['type' => 'join', 'path' => $as];
        // when searching in the child, we want to the parent to be specific
        // parent~1234567890, want to use ids and not abstract parent~
        $last = $this->breadcrumbs[count($this->breadcrumbs) - 2];
        $this->queryBuilder->wrapJoin($last['path'], $from, $as);
    }

    public function appendWhere(string $key, string $operator, mixed $value): void
    {
        $this->queryBuilder->appendWhere($key, $operator, $value);
    }

    public function appendOrderBy(string $key, string $direction): void
    {
        $this->queryBuilder->appendOrderBy($key, $direction);
    }

    public function getLimit(): ?int
    {
        return $this->queryBuilder->getLimit();
    }

    public function setLimit(int $limit): void
    {
        $this->queryBuilder->setLimit($limit);
    }

    public function setOffset(int $offset): void
    {
        $this->queryBuilder->setOffset($offset);
    }

    /**
     * This is a function to fetch one data. Most of the time, the data
     * is already present because it was fetched before during a cached query.
     * When the data is not present, we want to fetch the data.
     */
    public function findOneData(string $id): mixed
    {
        // Ensure that the content is initialized
        $this->queryBuilder->setSelect([$id]);
        $this->init();
        // Check if content is present
        // If key is not present, then the query is never cached before
        $result = $this->getContentOfThisLevel();
        if ($result && !empty($result["data"][$id])) {
            return $result["data"][$id];
        }
        // Get the content and cache the selection
        $query = $this->queryBuilder;
        $query->setOptions([
            'patch_cache_select' => true,
            'only_first'         => true,
            'use_cache'          => false,
        ]);
        $query->setSelect([$id]);
        $result = $query->run($this->isFake);
        if (count($result) === 0) {
            return null;
        }
        return $this->getContentOfThisLevel($result)['data'][$id] ?? null;
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
            'patch_cache_join'                => true,
            'only_first'                      => true,
            'use_cache_only_missing_children' => true,
            'use_cache'                       => false,
        ]);
        /// so we can use where and so forth
        $result = $child->run($this->isFake);
        if (count($result) === 0) {
            return null;
        }
        return $this->getContentOfThisLevel($result);
    }

    // After findFirstOfJoin we can use this function
    // to load the rest of the items with all cached queries
    public function findRestOfJoin(): ?array
    {
        $child = clone $this->queryBuilder;
        $child->setOptions([
            'use_cache'                       => true,
            'use_cache_only_missing_children' => true,
        ]);
        $child->ignoreFirstRow();
        $result        = $child->run($this->isFake);
        $this->content = $result;
        return $this->getContentOfThisLevel($result);
    }

    public function getContentOfThisLevel(array $content = null): ?array
    {
        $content ??= $this->content;
        foreach ($this->breadcrumbs as $breadcrumb) {
            switch ($breadcrumb['type']) {
                case 'id':
                    // We already are on the correct level
                    if (!empty($content['id'])) {
                        break;
                    }
                    // Find the correct level in an array
                    $found = false;
                    foreach ($content as $item) {
                        if ($item['id'] === $breadcrumb['path']) {
                            $content = $item;
                            $found   = true;
                        }
                    }
                    if (!$found) {
                        return null;
                    }
                    break;
                case 'join':
                    if (array_key_exists($breadcrumb['path'], $content['join'] ?? []) === false) {
                        return null;
                    }
                    $content = $content['join'][$breadcrumb['path']];
                    break;
            }
        }
        return $content;
    }

    public function __clone(): void
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }
}
