<?php

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentEntity;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ContentStore;
use Confetti\Helpers\ConditionDoesNotMatchConditionFromContent;
use IteratorAggregate;
use Traversable;

abstract class List_
{
    /**
     * The items contained in the collection.
     *
     * @var array<Map>
     */
    protected array $items = [];

    protected string $componentKey;
    private ContentStore $contentStore;

    public function __construct(
        protected string        $parentContentId,
        protected string        $relativeContentId,
        protected ContentStore  &$parentContentStore,
        private readonly string $as,
    )
    {
        $this->componentKey = ComponentStandard::componentKeyFromContentId($this->relativeContentId);
        $this->contentStore = clone $this->parentContentStore;
        $this->contentStore->join($this->relativeContentId, $as);
    }

    public function getId(): string
    {
        if ($this->relativeContentId === null) {
            throw new \RuntimeException("Component {{ $this->componentKey }} is used as a reference, so you can't call getId().");
        }
        return ComponentStandard::mergeIds($this->parentContentId, $this->relativeContentId);
    }

    /**
     * @return ComponentEntity[]
     */
    public function getComponentsFromChildren(): array
    {
        throw new \RuntimeException("Component {{ $this->componentKey }} is used as a reference, so you can't call getComponentsFromChildren().");
    }

    public function where(string|ComponentStandard $key, string $operator, mixed $value): self
    {
        if ($key instanceof ComponentStandard) {
            $key = $key::getComponentKey();
        }
        if ($value instanceof ComponentStandard) {
            $value = $value::getComponentKey();
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
     * @internal This method is not part of the public API and should not be used.
     */
    protected function generates(): string
    {
        throw new \RuntimeException('This method `generate` should be overridden in the child class.');
    }

    /**
     * @return \IteratorAggregate|Map[]
     * @noinspection PhpDocSignatureInspection
     */
    public function get(): IteratorAggregate
    {
        // Ensure that the content is initialized
        $runInit = $this->contentStore->runInit();
        if ($runInit) {
            // When the content is init (because of the list is the first component),
            // we want to use the content for the parent. So the parent has all the data.
            $this->parentContentStore->setContent($this->contentStore->getContent());
        }
        $className = $this->generates();

        // Most of the time we run the entire query once. But when we are
        // missing some data, we want to run a second query very efficiently
        // to prevent n+1 problems. With yield, we can fetch the first item and
        // cache the part of the query. When we now the first item, the query is
        // cached, and we can fetch the rest of the items in one go. Traditionally, with
        // an n+1 problem, the number of queries is equal to the number of items x child items.
        // With this method, the number of queries is less than the number of component types. Most
        // of the time, the number of component types is less than 2 because when you adjust one part
        // (in the middle) of the query, we can use the cached query to retrieve the rest of the query.
        return new class($this->parentContentId, $this->relativeContentId, $this->contentStore, $this->as, $className) implements IteratorAggregate {
            public function __construct(
                protected string       $parentContentId,
                protected string       $relativeContentId,
                protected ContentStore $contentStore,
                protected string       $as,
                protected string       $className,
            )
            {
            }

            public function toArray(): array
            {
                return iterator_to_array($this);
            }

            public function getIterator(): Traversable
            {
                if ($this->contentStore->canFake() && $this->contentStore->isFake()) {
                    foreach ($this->getFakeComponents($this->className) as $item) {
                        yield $item;
                    }
                    return;
                }
                try {
                    // Get items if present
                    $items = $this->contentStore->getContentOfThisLevel();
                } catch (ConditionDoesNotMatchConditionFromContent) {
                    // When the content is present but received with another query condition
                    $this->contentStore->runCurrentQuery([
                        'use_cache'               => true,
                        'response_with_condition' => true, // The children need to know if the data is retrieved with the same conditions.
                    ]);
                    $items = $this->contentStore->getContentOfThisLevel();
                }

                // If items are present, but without data. Then it looks useless,
                // but we can use to skip findFirstOfJoin()
                $firstEmptyContent = $this->getFirstEmptyContent($items);

                // If data is present and not empty, then we can use it
                if ($items !== null && $firstEmptyContent === null) {
                    if ($this->contentStore->canFake() && count($items) === 0) {
                        foreach ($this->getFakeComponents($this->className) as $item) {
                            yield $item;
                        }
                        return;
                    }
                    $class = ComponentStandard::componentClassById(
                        ComponentStandard::mergeIds($this->parentContentId, $this->relativeContentId),
                        $this->contentStore,
                    );
                    if ($class instanceof \Exception) {
                        throw $class;
                    }
                    $this->className = $class;
                    foreach ($items as $item) {
                        $childContentStore = clone $this->contentStore;
                        $childContentStore->appendCurrentJoin($item['id']);
                        yield new $this->className($this->parentContentId, $item['id'], $childContentStore, $this->as);
                    }
                    return;
                }

                // $firstEmptyContent can be loaded due init
                // When the content is not present, we want to load all the data
                // But to prevent n+1 the problem, we need to load the first item.
                $first = $firstEmptyContent ?? $this->contentStore->findFirstOfJoin()[0] ?? null;
                // If key not even present, then we need to use the fake components
                if ($this->contentStore->canFake() && $first === null) {
                    foreach ($this->getFakeComponents($this->className) as $item) {
                        yield $item;
                    }
                    return;
                }
                if (empty($first)) {
                    return;
                }
                $childContentStore = clone $this->contentStore;
                $childContentStore->appendCurrentJoin($first['id']);
                yield new $this->className($this->parentContentId, $first['id'], $childContentStore);

                // After the first item is loaded and cached, we can load the rest of the items in one go.
                $contents = $this->contentStore->findRestOfJoin() ?? [];
                foreach ($contents as $content) {
                    $childContentStore = clone $this->contentStore;
                    $childContentStore->appendCurrentJoin($content['id']);
                    yield new $this->className($this->parentContentId, $content['id'], $childContentStore);
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
                if (array_key_exists('id', $items)) {
                    throw new \RuntimeException('Error htrj8945h: can\'t get first item of list, array of items expected, but id found in the root');
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
                $contentId = ComponentStandard::mergeIds($this->parentContentId, $this->relativeContentId);

                // Get the number of items. If not present,
                // then use default values. To prevent rendering too
                // many items, we don't fake to many items in deeper levels.
                $deeper = $this->contentStore->isFake();
                $max    = $this->contentStore->getLimit() ?? $component->getDecoration('max')['value'] ?? ($deeper ? 5 : 20);
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

    public function first(): Map|ComponentStandard|null
    {
        $this->contentStore->setLimit(1);
        return $this->get()->getIterator()->current();
    }

    /**
     * @return array<string, array<string, \Confetti\Helpers\ContentEntity[]>>
     */
    public static function getColumns(
        \Confetti\Components\List_ $model,
    ): array
    {
        $children = $model->getComponentsFromChildren();
        // Get defined columns if possible
        $definedColumns = $model->getComponent()->getDecoration('columns');
        if ($definedColumns) {
            return array_map(static function (array $column) use ($model, $children) {
                // find default value (from getDecoration) from children
                $defaultValue = null;
                foreach ($children as $child) {
                    if ($child->key === $model->getId() . '/' . $column['id']) {
                        $defaultValue = $child->getDecoration('default');
                        break;
                    }
                }
                return [
                    'id'            => $column['id'],
                    'label'         => $column['label'],
                    'default_value' => $defaultValue,
                ];
            }, $definedColumns);
        }

        // If columns are not defined, then get the first 4 text columns
        // Filter out non-text columns
        $columns = array_filter($children, static fn(ComponentEntity $column) => $column->type === 'text');
        // Get max 4 columns
        $columns = array_slice($columns, 0, 4);
        return array_map(static function (ComponentEntity $column) {
            $key = explode('/', $column->key);
            $key = end($key);
            return [
                'id'            => $key,
                'label'         => ucwords(str_replace('_', ' ', $key)),
                'default_value' => $column->getDecoration('default'),
            ];
        }, $columns);
    }

    protected function decode(string $json): mixed
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return 'Error 7o8h5edg4n5jk: can\'t decode options: ' . $json . ', Message ' . $e->getMessage();
        }
    }

    // Label is used as a field title in the admin panel
    public function label(string $label): self
    {
        return $this;
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