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

    /** @noinspection DuplicatedCode */
    public function __construct(
        protected string         $contentId,
        protected ComponentStore $componentStore,
        protected ContentStore   $contentStore,
        string $as,
    )
    {
        $this->contentId    .= '~';
        $this->componentKey = ComponentStandard::componentKeyFromContentId($this->contentId);
        $this->contentStore->join($this->contentId, $as);
    }

    /**
     * @return \IteratorAggregate|Map[]
     * @noinspection PhpDocSignatureInspection
     */
    public function get(): IteratorAggregate
    {
        // Ensure that the content is initialized
        // @todo get data from already existing loaded data

        // @todo cache query

        // When the content is not present, we want to load all the data
        // But to prevent n+1 problem, we need to load the first item
        // and then load the rest of the items in one go
        return new class($this->contentId, $this->componentStore, $this->contentStore) implements IteratorAggregate {
            public function __construct(
                protected string         $contentId,
                protected ComponentStore $componentStore,
                protected ContentStore   $contentStore,
            )
            {
            }

            public function getIterator(): Traversable
            {
                $content = $this->contentStore->findOneOfMany($this->contentId);
                if ($content === null) {
                    return;
                }
                $first = $content[0];
                $class = ComponentStandard::componentClassFromKey($this->contentId);
                yield new $class($first['id'], $this->componentStore, $this->contentStore);
//                yield new $class('todo2', $this->componentStore, $this->contentStore);
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

    private function getFakeComponents(): array
    {
        return [];
//        $component = $this->componentStore->find($this->componentKey);
//
//        $max = $component->getDecoration('max')['value'] ?? 100;
//        $min = $component->getDecoration('min')['value'] ?? 1;
//
//        $amount = random_int($min, $max);
//
//        $i     = 1;
//        $items = [];
//        while ($i <= $amount) {
//            $i++;
//            $items[] = new Map(
//                $this->contentId,
//                $this->componentStore,
//                $this->contentStore,
//            );
//        }
//        return $items;
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