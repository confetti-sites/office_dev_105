<?php

declare(strict_types=1);

namespace Confetti\Helpers;

class ContentStore
{
    private QueryBuilder $queryBuilder;
    private array $content = [];
    public bool $alreadyInit = false;
    // This is a fake store, used for mocking
    // data for development. No database queries are made
    private bool $isFake = false;
    // The data can be real, but the store is allowed to fake.
    private bool $fakeMaker = false;

    /**
     * @var array array with 'type' and 'path'
     */
    private array $breadcrumbs = [];

    public function __construct(string $from, string $as)
    {
        $this->breadcrumbs[] = ['type' => 'id', 'path' => $from];
        $this->queryBuilder  = new QueryBuilder($from, $as);
    }

    public function runInit(): bool
    {
        if ($this->alreadyInit) {
            return false;
        }
        if ($this->isFake) {
            $this->content     = [];
            $this->alreadyInit = true;
            return true;
        }
        $this->queryBuilder->setOptions([
            'use_cache'               => true,
            'use_cache_from_root'     => true, // We want all the data. Even if it is for the parent.
            'patch_cache_select'      => true,
            'response_with_condition' => true, // We want to know if the data is retrieved with the same conditions.
        ]);
        // Get the first item. The data we want to use is in the join.
        $this->content     = $this->queryBuilder->run()[0] ?? [];
        $this->alreadyInit = true;
        return true;
    }

    public function runCurrentQuery(): array|null
    {
        $this->queryBuilder->setOptions([
            'use_cache'               => true,
            'response_with_condition' => true, // The children need to know if the data is retrieved with the same conditions.
        ]);
        // Get the first item. The data we want to use is in the join.
        $this->content = $this->isFake ? []: $this->queryBuilder->run()[0] ?? [];
        return $this->getContentOfThisLevel();
    }

    public function isFake(): bool
    {
        return $this->isFake;
    }

    public function setIsFake(): void
    {
        $this->isFake = true;
    }

    public function isFakeMaker(): bool
    {
        return $this->fakeMaker;
    }

    public function setFakeMaker(bool $fakeMaker): void
    {
        $this->fakeMaker = $fakeMaker;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): void
    {
        $this->content = $content;
        // When the content is set, during the init of a child,
        // we want to set the alreadyInit to true. This is because
        // we don't want to fetch the content again.
        $this->alreadyInit = true;
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
        // Go back 1 to match the fact that the first item is on index 0.
        $last = $this->breadcrumbs[count($this->breadcrumbs) - 1];
        // When we run a query from a component: `\model\page\banner_list_title::query()->get()`
        // we have multiple titles, but the title itself is not a list, and whe don't want to use join.
        $this->breadcrumbs[] = ['type' => 'join', 'path' => $as];
        // When searching in the child, we want to the parent to be specific
        // parent~1234567890, want to use ids and not abstract parent~.
        $this->queryBuilder->wrapJoin($last['path'], $from, $as);
    }

    public function select(mixed ...$select): void
    {
        $this->queryBuilder->setSelect($select);
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
        $this->runInit();
        // Check if content is present
        // If key is not present, then the query is never cached before
        try {
            $result = $this->getContentOfThisLevel();
            if ($result && array_key_exists($id, $result["data"])) {
                return $result["data"][$id];
            }
        } catch (ConditionDoesNotMatchConditionFromContent) {
            // We need to fetch the content with the correct condition
        }
        // Query the content and cache the selection
        $query = $this->queryBuilder;
        $query->setOptions([
            'patch_cache_select' => true,
            'only_first'         => true,
            'use_cache'          => false,
        ]);
        $query->setSelect([$id]);
        $result = $this->isFake ? []: $query->run();
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
            $this->runInit();
        }
        $child = $this->queryBuilder;
        // Get the content and cache the selection
        $child->setOptions([
            'patch_cache_join'        => true,
            'only_first'              => true,
            'use_cache'               => true,
            'response_with_condition' => true,
        ]);
        /// so we can use where and so forth
        $result = $this->isFake ? []: $child->run();
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
            'use_cache'               => true,
            // although we ignore the condition in the next getContentOfThisLevel,
            // we still want to know if the data is retrieved with the same conditions
            // for when the children need to know if the data is retrieved with the same conditions.
            'response_with_condition' => true,
        ]);
        $child->ignoreFirstRow();
        $result        = $this->isFake ? []: $child->run();
        $this->content = $result;
        return $this->getContentOfThisLevel($result, true);
    }

    public function getContentOfThisLevel(array $content = null, bool $ignoreCondition = false): ?array
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
                    // We need to check if the condition is the same.
                    // Data from joins with dynamic conditions do
                    // not always match the result when a query is cached.
                    // We need to be able to verify if the data from
                    // the cache matches the data from the given condition.
                    $currentCondition    = $this->queryBuilder->getCurrentCondition();
                    $checkIfQueryMatches = !$ignoreCondition && array_key_exists($breadcrumb['path'], $content['join_condition'] ?? []);
                    $contentCondition    = $checkIfQueryMatches ? $content['join_condition'][$breadcrumb['path']] : null;
                    if ($checkIfQueryMatches && $contentCondition !== $currentCondition) {
                        throw new ConditionDoesNotMatchConditionFromContent("The query that is used to fetch the data is not the same as the query that is used to generate the response. This is a bug in Confetti. Given condition: `{$currentCondition}`, response condition: `$contentCondition`");
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
