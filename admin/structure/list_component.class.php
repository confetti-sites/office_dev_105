<?php

declare(strict_types=1);

namespace Confetti\Components;

use ArrayIterator;
use Confetti\Helpers\ComponentEntity;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;
use IteratorAggregate;

/**
 * @implements \IteratorAggregate<string, Map>
 */
return new class implements IteratorAggregate {
    /**
     * The items contained in the collection.
     *
     * @var array<string, Map>
     */
    protected array $items = [];

    protected string $key;

    /** @noinspection DuplicatedCode */
    public function __construct(
        protected string         $id,
        protected ComponentStore $componentStore,
        protected ContentStore   $contentStore,
    )
    {
        $this->id  .= '~';
        $this->key = ComponentStandard::keyFromId($this->id);

        $items = $this->contentStore->findMany($this->id);
        if (count($items) === 0) {
            $this->items = $this->getFakeComponents();
            return;
        }

        foreach ($items as $item) {
            $this->items[] = new Map($item->id, $this->componentStore, $this->contentStore);
        }
    }

    /**
     * @return array<string, array<string, \Confetti\Helpers\ContentEntity[]>>
     */
    public static function getColumnsAndRows(
        ComponentEntity $component,
        ContentStore    $contentStore,
        string          $contentId,
    ): array
    {
        $columns = $component->getDecoration('columns')['columns'] ?? throw new \Exception('Error: No columns defined. Use ->columns([]) to define columns. In ' . $component->source);
        $fields  = array_map(static fn($column) => $column['id'], $columns);

        $data = $contentStore->whereIn($contentId, $fields, true);

        // Make rows by grouping on the id minus the relative id
        $rows = [];
        foreach ($data as $item) {
            // Ensure row exists even if there is no data
            if ($item->value === '__is_parent') {
                $rows[$item->id] = $rows[$item->id] ?? [];
                continue;
            }

            // Trim relative id
            $regex             = '/\/(?:' . implode('|', $fields) . ')$/';
            $parentId          = preg_replace($regex, '', $item->id, 1);
            $rows[$parentId][] = $item;
        }

        return [$columns, $rows];
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator<string, Map>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param string $key
     * @return Map
     */
    public function offsetGet($key): mixed
    {
        return $this->items[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param string $key
     * @param Map    $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }

    private function getFakeComponents(): array
    {
        $component = $this->componentStore->find($this->key);

        $max = $component->getDecoration('max')['value'] ?? 100;
        $min = $component->getDecoration('min')['value'] ?? 1;

        $amount = random_int($min, $max);

        $i     = 1;
        $items = [];
        while ($i <= $amount) {
            $i++;
            $items[] = new Map(
                $this->id,
                $this->componentStore,
                $this->contentStore,
            );
        }
        return $items;
    }
};
