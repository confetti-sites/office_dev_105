<?php

declare(strict_types=1);

namespace Confetti\Helpers;

class ContentStore
{
    private QueryBuilder $queryBuilder;
    private array $content = [];
    private bool $alreadyInit = false;

    /**
     * @var array array with 'type' and 'path'
     */
    private array $breadcrumbs = [];

    public function __construct(string $from, string $as)
    {
        $this->breadcrumbs[] = ['type' => 'id', 'path' => $from];
        $this->queryBuilder  = new QueryBuilder($from, $as);
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
        $this->queryBuilder->setOptions([
            'use_cache'          => true,
            'patch_cache_select' => true,
        ]);
        $this->content     = $this->queryBuilder->run()[0] ?? ['join' => []];
        $this->alreadyInit = true;
    }

    public function setCurrentLevel(string $relativeId): void
    {
        $this->breadcrumbs[] = ['type' => 'id', 'path' => $relativeId];
    }

    public function join(string $from, string $as): void
    {
        $this->breadcrumbs[] = ['type' => 'join', 'path' => $as];
        // when searching in the child, we want to the parent to be specific
        // parent~1234567890, want to use ids and not abstract parent~
        $last = $this->breadcrumbs[count($this->breadcrumbs) - 2];
        $this->queryBuilder->wrapJoin($last['path'], $from, $as);
    }

    public function findOneData(string $id): mixed
    {
        // Ensure that the content is initialized
        $this->queryBuilder->setSelect([$id]);
        $this->init();
        // Check if content is present
        // If key is not present, then the query is never cached before
        $result = $this->getContentOfThisLevel();
        if ($result) {
            if (empty($result["data"][$id])) {
                return null;
            }
            return $result["data"][$id];
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
        return $this->getContentOfThisLevel($result)['data'][$id];
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
        $result = $child->run();
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
        $result        = $child->run();
        $this->content = $result;
        return $this->getContentOfThisLevel($result);
    }

    public function getContentOfThisLevel(array $content = null): ?array
    {
        $content ??= $this->content;
//        echo '<pre>';
//        var_dump($this->breadcrumbs);
//        var_dump($content);
//        echo '</pre>';
        foreach ($this->breadcrumbs as $breadcrumb) {
            switch ($breadcrumb['type']) {
                case 'id':
                    // We already are on the correct level
                    if (!empty($content['id'])) {
                        break;
                    }
                    // Find the correct level in an array
                    foreach ($content as $item) {
                        if ($item['id'] === $breadcrumb['path']) {
                            $content = $item;
                        }
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
