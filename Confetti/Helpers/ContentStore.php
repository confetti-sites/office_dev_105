<?php

declare(strict_types=1);

namespace Confetti\Helpers;

class ContentStore
{
    private \Confetti\Helpers\QueryBuilder $queryBuilder;
    private array $content = [];
    private bool $alreadyInit = false;
    // This is a fake store, used for mocking
    // data for development. No database queries are made
    private bool $isFake = false;
    // The data can be real, but the store is allowed
    // to generate fake data. For now, this is static. Later on,
    // we can make this so it can be passed on to the child components.
    private static ?bool $canFake = null;

    /**
     * @var array array with 'type' and 'path'
     */
    private array $breadcrumbs = [];

    public function __construct(string $from, string $as)
    {
        exit('this is a test');
        if (self::$canFake === null) {
            self::$canFake = envConfig('options.when_no_data_is_saved_show_fake_data', false);
        }

        // In the root, we want to select general data.
        // For example, the cached pointer keys.
        // We use /model as the root, because we want to
        // the rest of the data from the tree.
        $this->queryBuilder  = new \Confetti\Helpers\QueryBuilder('/model', $as);
        $this->breadcrumbs[] = ['type' => 'id', 'path' => '/model'];

        // One level deeper, we want to select other data from the tree.
        // So we want to point to another query and data higher in the tree.
        if ($from !== '/model') {
            $this->joinPointer($from);
        }
    }

    public function runInit(): bool
    {
        if ($this->alreadyInit) {
            return false;
        }
        if ($this->isFake) {
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

    public function selectInRoot(...$select): void
    {
        $this->queryBuilder->appendSelectInRoot(...$select);
    }

    public function runCurrentQuery($options): void
    {
        $this->queryBuilder->setOptions($options);
        // Get the first item. The data we want to use is in the join.
        $result = [];
        if (!$this->isFake) {
            $result = $this->queryBuilder->run()[0] ?? [];
        }
        $this->content = $this->mergeRecursive($this->content, $result);
    }

    public function isFake(): bool
    {
        return $this->isFake;
    }

    public function setIsFake(): void
    {
        $this->isFake = true;
    }

    public function canFake(): bool
    {
        return self::$canFake;
    }

    public function setCanFake(bool $canFake): void
    {
        self::$canFake = $canFake;
    }

    public function resetBreadcrumbs(): void
    {
        $this->breadcrumbs = [];
    }

    public function getLatestBreadcrumb(): array
    {
        // We don't use end() because we want
        // don't want to change the array pointer
        return $this->breadcrumbs[count($this->breadcrumbs) - 1];
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
        // Note: when can run a query from a component: `\model\page\banner_list\title::query()->get()`
        // Than we have multiple titles, but the title itself is not a list.
        $this->breadcrumbs[] = ['type' => 'join', 'path' => $as];
        // When searching in the child, we want to the parent to be specific
        // parent~1234567890, we want to use ids and not abstract parent~.
        $this->queryBuilder->wrapJoin($last['path'], $from, $as);
    }

    public function joinPointer(string $from): void
    {
        // Go back 1 to match the fact that the first item is on index 0.
        $last = $this->breadcrumbs[count($this->breadcrumbs) - 1];
        // When we run a query from a component: `\model\page\banner_list\title::query()->get()`
        // we have multiple titles, but the title itself is not a list, and whe don't want to use join.
        $this->breadcrumbs[] = ['type' => 'join_pointer', 'path' => $from];
        // When searching in the child, we want to the parent to be specific
        // parent~1234567890, we want to use ids and not abstract parent~.
        $this->queryBuilder->wrapJoin($last['path'], $from, $from);
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
    public function findOneData(string $parentId, string $relativeId): mixed
    {
        if ($this->isFake) {
            return null;
        }
        if (str_ends_with($relativeId, '-')) {
            return $this->findPointerData(\Confetti\Helpers\ComponentStandard::mergeIds($parentId, $relativeId));
        }
        return $this->findSelectedData($relativeId);
    }

    private function findSelectedData(string $id): mixed
    {
        if (!$this->alreadyInit) {
            $this->queryBuilder->setSelect([$id]);
            // Ensure that the content is initialized
            $this->runInit();
        }
        // Check if content is present
        // If key is not present, then the query is never cached before
        try {
            [$result, $found] = $this->getContentOfThisLevelById($id);
            if ($found) {
                return $result;
            }
        } catch (\Confetti\Helpers\ConditionDoesNotMatchConditionFromContent) {
            // We need to fetch the content with the correct condition
        }
        // Query the content and cache the selection
        $query = $this->queryBuilder;
        $query->setOptions([
            'patch_cache_select'    => true,
            'only_first'            => true,
            'use_cache'             => false,
        ]);
        $query->setSelect([$id]);
        $result = $query->run();
        if (count($result) === 0) {
            return null;
        }
        [$result, $found] = $this->getContentOfThisLevelById($id, $result);
        return $result;
    }

    public function findPointerData(string $id): mixed
    {
        if (!$this->alreadyInit) {
            $this->selectInRoot($id);
            $this->runInit();
            if (!array_key_exists('data', $this->content)) {
                return null;
            }
            return $this->content['data'][$id];
        }

        if (array_key_exists($id, $this->content['data'])) {
            return $this->content['data'][$id];
        }
        $this->selectInRoot($id);
        $this->runCurrentQuery([
            'use_cache'               => true,
            'use_cache_from_root'     => true,
            'patch_cache_select'      => true,
        ]);
        return $this->content['data'][$id] ?? null;
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
        $result = $this->isFake ? [] : $child->run();
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
        $result        = $this->isFake ? [] : $child->run();
        $this->content = $result;
        return $this->getContentOfThisLevel($result, true);
    }

    /**
     * @return <array, bool> The first value is the content,
     * the second value is a boolean that indicates if the key
     * is found. If the key is found, there is no reason to fetch the data again.
     * @noinspection PhpDocSignatureInspection
     */
    public function getContentOfThisLevelById(string $id, array $content = null): array
    {
        $result = $this->getContentOfThisLevel($content);

        // The data from a pointer is from a join with an array of data (always one).
        if ($this->getLatestBreadcrumb()['type'] === 'join_pointer' && str_ends_with($id, '-')) {
            if (!empty($result) && array_key_exists($id, $result[0]['data'])) {
                return [$result[0]["data"][$id], true];
            }
            return [null, false];
        }
        if ($result && array_key_exists($id, $result["data"])) {
            return [$result["data"][$id], true];
        }
        return [null, false];
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
                        if (!array_key_exists('id', $item)) {
                            continue;
                        }
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
                        throw new \Confetti\Helpers\ConditionDoesNotMatchConditionFromContent("The query that is used to fetch the data is not the same as the query that is used to generate the response. This is a bug in Confetti. Given condition: `{$currentCondition}`, response condition: `$contentCondition`");
                    }
                    $content = $content['join'][$breadcrumb['path']];
                    break;
                case 'join_pointer':
                    if (empty($content['join'][$breadcrumb['path']])) {
                        return null;
                    }
                    $content = $content['join'][$breadcrumb['path']][0];
                    break;
            }
        }

        return $content;
    }

    public function __clone(): void
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }

    private function mergeRecursive(array $array1, array $array2): array
    {
        if (empty($array1)) {
            return $array2;
        }
        if (empty($array2)) {
            return $array1;
        }
        $merged = $array1;
        foreach ($array2 as $key => $value) {
            if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key])) {
                $merged[$key] = $this->mergeRecursive($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}
