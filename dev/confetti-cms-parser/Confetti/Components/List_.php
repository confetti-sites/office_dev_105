<?php

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentEntity;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;
use IteratorAggregate;
use Traversable;

class List_
{
    /**
     * The items contained in the collection.
     *
     * @var array<Map>
     */
    protected array $items = [];

    protected string $componentKey;
    private string $as;

    /** @noinspection DuplicatedCode */
    public function __construct(
        protected string         $parentContentId,
        protected string         $relativeContentId,
        protected ComponentStore &$componentStore,
        protected ContentStore   $contentStore,
        string                   $as,
    )
    {
        $this->relativeContentId .= '~';
        $this->as                = $as;
        $this->componentKey      = ComponentStandard::componentKeyFromContentId($this->relativeContentId);
        $this->contentStore      = clone $this->contentStore;
        $this->contentStore->join($this->relativeContentId, $as);
    }

    public function where(string|ComponentStandard $key, string $operator, mixed $value): self
    {
        if ($key instanceof ComponentStandard) {
            $key = $key->getComponentKey();
        }
        if ($value instanceof ComponentStandard) {
            $value = $value->getComponentKey();
        }
        $this->contentStore->appendWhere($key, $operator, $value);
        return $this;
    }

    // Example of descending order:
    // 5, 4, 3, 2, 1
    public function orderDescBy(string|ComponentStandard $key): self
    {
        if ($key instanceof ComponentStandard) {
            $key = $key->getComponentKey();
        }
        $this->contentStore->appendOrderBy($key, 'descending');
        return $this;
    }

    // Example of ascending order:
    // 1, 2, 3, 4, 5
    public function orderAscBy(string|ComponentStandard $key): self
    {
        if ($key instanceof ComponentStandard) {
            $key = $key->getComponentKey();
        }
        $this->contentStore->appendOrderBy($key, 'ascending');
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->contentStore->setLimit($limit);
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->contentStore->setOffset($offset);
        return $this;
    }

    /**
     * @return \IteratorAggregate|Map[]
     * @noinspection PhpDocSignatureInspection
     */
    public function get(): IteratorAggregate
    {
        // Ensure that the content is initialized
        $this->contentStore->init();

        // Most of the time we run the entire query once. But when we are
        // missing some data, we want to run a second query very efficiently
        // to prevent n+1 problems. With yield, we can fetch the first item and
        // cache the part of the query. When we now the first item, the query is
        // cached, and we can fetch the rest of the items in one go. Traditionally, with
        // an n+1 problem, the number of queries is equal to the number of items x child items.
        // With this method, the number of queries is less than the number of component types. Most
        // of the time, the number of component types is less than 2 because when you adjust one part
        // (in the middle) of the query, we can use the cached query to retrieve the rest of the query.
        return new class($this->parentContentId, $this->relativeContentId, $this->componentStore, $this->contentStore, $this->as) implements IteratorAggregate {
            public function __construct(
                protected string         $parentContentId,
                protected string         $relativeContentId,
                protected ComponentStore &$componentStore,
                protected ContentStore   $contentStore,
                protected string         $as,
            )
            {
            }

            public function getIterator(): Traversable
            {
                $class = ComponentStandard::componentClassByContentId($this->parentContentId, $this->relativeContentId);
                if ($this->contentStore->isFake()) {
                    foreach ($this->getFakeComponents($class) as $item) {
                        yield $item;
                    }
                    return;
                }
                // Get items if present
                $items = $this->contentStore->getContentOfThisLevel();
                // If items are present, but without data. Then it looks useless,
                // but we can use to skip findFirstOfJoin()
                $firstEmptyContent = $this->getFirstEmptyContent($items);

                // If data is present and useful, then we can use it
                if ($items !== null && $firstEmptyContent === null) {
                    if (count($items) === 0) {
                        foreach ($this->getFakeComponents($class) as $item) {
                            yield $item;
                        }
                        return;
                    }
                    $class = ComponentStandard::componentClassByContentId($this->parentContentId, $this->relativeContentId);
                    foreach ($items as $item) {
                        $childContentStore = clone $this->contentStore;
                        $childContentStore->appendCurrentJoin($item['id']);
                        yield new $class($this->parentContentId, $item['id'], $this->componentStore, $childContentStore, $this->as);
                    }
                    return;
                }

                // $firstEmptyContent can be loaded due init
                // When the content is not present, we want to load all the data
                // But to prevent n+1 problem, we need to load the first item.
                $firstEmptyContent ??= $this->contentStore->findFirstOfJoin()[0] ?? null;
                if ($firstEmptyContent === null) {
                    foreach ($this->getFakeComponents($class) as $item) {
                        yield $item;
                    }
                    return;
                }
                if (!empty($firstEmptyContent)) {
                    $childContentStore = clone $this->contentStore;
                    $childContentStore->appendCurrentJoin($firstEmptyContent['id']);
                    yield new $class($this->parentContentId, $firstEmptyContent['id'], $this->componentStore, $childContentStore);
                }

                // After the first item is loaded and cached, we can load the rest of the items in one go.
                $contents = $this->contentStore->findRestOfJoin();
                foreach ($contents as $content) {
                    $childContentStore = clone $this->contentStore;
                    $childContentStore->appendCurrentJoin($content['id']);
                    yield new $class($this->parentContentId, $content['id'], $this->componentStore, $childContentStore);
                }
            }

            /**
             * If items are present, but without data. Then it looks useless,
             * but we can use to skip findFirstOfJoin()
             */
            private function getFirstEmptyContent(?array $items): ?array
            {
                if ($items === null) {
                    return null;
                }
                if (count($items) > 0 && empty($items[0]['data'] && empty($items[0]['join']))) {
                    return $items[0];
                }
                return null;
            }

            private function getFakeComponents(string $class): array
            {
                $contentId = ComponentStandard::mergeIds($this->parentContentId, $this->relativeContentId);
                $component = $this->componentStore->find($contentId);
                $deeper    = $this->contentStore->isFake();

                // Get the number of items. If not present,
                // then use default values. To prevent rendering too
                // many items, we don't fake to many items in deeper levels.
                $max    = $this->contentStore->getLimit() ?? $component->getDecoration('max')['value'] ?? ($deeper ? 5 : 50);
                $min    = $component->getDecoration('min')['value'] ?? 1;
                $amount = random_int($min, $max);

                $i     = 1;
                $items = [];
                while ($i <= $amount) {
                    $childContentStore = clone $this->contentStore;
                    $childContentStore->appendCurrentJoin($contentId);
                    $childContentStore->setIsFake();
                    $i++;
                    $idSuffix = str_pad((string) $i, 10, '0', STR_PAD_LEFT);
                    $items[]  = new $class(
                        $this->parentContentId,
                        $contentId . $idSuffix,
                        $this->componentStore,
                        $childContentStore,
                    );
                }
                return $items;
            }
        };
    }

    /**
     * @return array<string, array<string, \Confetti\Helpers\ContentEntity[]>>
     */
    public static function getColumnsAndRows(
        ComponentStore  $componentStore,
        ComponentEntity $component,
        ContentStore    $contentStore,
        string          $contentId,
    ): array
    {
        return [];
//        $columns = $component->getDecoration('columns')['columns'] ?? null;
//        if ($columns === null) {
//            // Use default columns
//            $columns = $componentStore->whereParentKey(ComponentStandard::componentKeyFromContentId($contentId));
//            $columns = array_filter($columns, static fn(ComponentEntity $column) => $column->type === 'text');
//            $columns = array_slice($columns, 0, 4);
//            $columns = array_map(static function (ComponentEntity $column) {
//                $key = explode('/', $column->key);
//                $key = end($key);
//                return ['id' => $key, 'label' => $key];
//            }, $columns);
//        }
//        $fields = array_map(static fn($column) => $column['id'], $columns);
//
//        $data = $contentStore->whereIn($contentId, $fields, true);
//
//        // Make rows by grouping on the id minus the relative id
//        $rows = [];
//        foreach ($data as $item) {
//            // Ensure row exists even if there is no data
//            if ($item->value === '__is_parent') {
//                $rows[$item->contentId] = $rows[$item->contentId] ?? [];
//                continue;
//            }
//
//            // Trim relative id
//            $regex             = '/\/(?:' . implode('|', $fields) . ')$/';
//            $parentId          = preg_replace($regex, '', $item->contentId, 1);
//            $rows[$parentId][] = $item;
//        }
//
//        return [$columns, $rows];
    }

    // Minimum number of items

    public function min(int $min): self
    {
        return $this;
    }

    // Maximum number of items

    public function max(int $max): self
    {
        return $this;
    }

    // This becomes the headers of the table in de admin

    public function columns(array $columns): self
    {
        return $this;
    }
}