<?php

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentEntity;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;
use Confetti\Helpers\ConditionDoesNotMatchConditionFromContent;
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
    private ContentStore $contentStore;

    /** @noinspection DuplicatedCode */
    public function __construct(
        protected string         $parentContentId,
        protected string         $relativeContentId,
        protected ContentStore   &$parentContentStore,
        private readonly string  $as,
    )
    {
        $this->relativeContentId .= '~';
        $this->componentKey      = ComponentStandard::componentKeyFromContentId($this->relativeContentId);
        $this->contentStore      = clone $this->parentContentStore;
        $this->contentStore->join($this->relativeContentId, $as);
    }

    public function getId(): string
    {
        if ($this->relativeContentId === null) {
            throw new \RuntimeException("Component {{ $this->componentKey }} is used as a reference, so there is no content id.");
        }
        return ComponentStandard::mergeIds($this->parentContentId, $this->relativeContentId);
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
        $runInit = $this->contentStore->init();
        if ($runInit) {
            // When the content is init (because of the list is the first component),
            // we want to use the content for the parent. So the parent has all the data.
            $this->parentContentStore->setContent($this->contentStore->getContent());
        }

        // Most of the time we run the entire query once. But when we are
        // missing some data, we want to run a second query very efficiently
        // to prevent n+1 problems. With yield, we can fetch the first item and
        // cache the part of the query. When we now the first item, the query is
        // cached, and we can fetch the rest of the items in one go. Traditionally, with
        // an n+1 problem, the number of queries is equal to the number of items x child items.
        // With this method, the number of queries is less than the number of component types. Most
        // of the time, the number of component types is less than 2 because when you adjust one part
        // (in the middle) of the query, we can use the cached query to retrieve the rest of the query.
        return new class($this->parentContentId, $this->relativeContentId, $this->contentStore, $this->as) implements IteratorAggregate {
            public function __construct(
                protected string         $parentContentId,
                protected string         $relativeContentId,
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
                try {
                    // Get items if present
                    $items = $this->contentStore->getContentOfThisLevel();
                } catch (ConditionDoesNotMatchConditionFromContent) {
                    // When the content is present but received with another query condition
                    $items = $this->contentStore->fetchCurrentQuery();
                }

                // If items are present, but without data. Then it looks useless,
                // but we can use to skip findFirstOfJoin()
                $firstEmptyContent = $this->getFirstEmptyContent($items);

                // If data is present and not empty, then we can use it
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
                        yield new $class($this->parentContentId, $item['id'], $childContentStore, $this->as);
                    }
                    return;
                }

                // $firstEmptyContent can be loaded due init
                // When the content is not present, we want to load all the data
                // But to prevent n+1 problem, we need to load the first item.
                $first = $firstEmptyContent ?? $this->contentStore->findFirstOfJoin()[0] ?? null;
                if ($first === null) {
                    foreach ($this->getFakeComponents($class) as $item) {
                        yield $item;
                    }
                    return;
                }

                if (empty($first)) {
                    return;
                }
                $childContentStore = clone $this->contentStore;
                $childContentStore->appendCurrentJoin($first['id']);
                yield new $class($this->parentContentId, $first['id'], $childContentStore);

                // After the first item is loaded and cached, we can load the rest of the items in one go.
                $contents = $this->contentStore->findRestOfJoin() ?? [];
                foreach ($contents as $content) {
                    $childContentStore = clone $this->contentStore;
                    $childContentStore->appendCurrentJoin($content['id']);
                    yield new $class($this->parentContentId, $content['id'], $childContentStore);
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
                if (count($items) > 0 && empty($items[0]['data']) && empty($items[0]['join'])) {
                    return $items[0];
                }
                return null;
            }

            private function getFakeComponents(string $class): array
            {
                /** @var ComponentEntity $component */
                $component = (new $class())->getComponent();
                $contentId  = ComponentStandard::mergeIds($this->parentContentId, $this->relativeContentId);

                // Get the number of items. If not present,
                // then use default values. To prevent rendering too
                // many items, we don't fake to many items in deeper levels.
                $deeper     = $this->contentStore->isFake();
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
                        $childContentStore,
                    );
                }
                return $items;
            }
        };
    }

    public function first(): ?Map
    {
        $this->contentStore->setLimit(1);
        return $this->get()->getIterator()->current();
    }

    /**
     * @return array<string, array<string, \Confetti\Helpers\ContentEntity[]>>
     */
    public static function getDefaultColumns(
        \Confetti\Components\List_ $model,
    ): array
    {
        // Use default columns
        $columns = $model->getChildren();
        echo '<pre>';
        var_dump($columns);
        echo '</pre>';
        exit('debug asdf');
        $columns = array_filter($columns, static fn(ComponentEntity $column) => $column->type === 'text');
        $columns = array_slice($columns, 0, 4);
        $columns = array_map(static function (ComponentEntity $column) {
            $key = explode('/', $column->key);
            $key = end($key);
            return ['id' => $key, 'label' => $key];
        }, $columns);
        return $columns;
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