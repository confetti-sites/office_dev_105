<?php

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentEntity;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;

return new class extends \Confetti\Helpers\QueryBuilder {
    /**
     * The items contained in the collection.
     *
     * @var array<string, Map>
     */
    protected array $items = [];

    protected string $componentKey;

    /** @noinspection DuplicatedCode */
    public function __construct(
        protected string         $contentId,
        protected ComponentStore $componentStore,
        protected ContentStore   $contentStore,
    )
    {
        $this->contentId    .= '~';
        $this->componentKey = ComponentStandard::componentKeyFromContentId($this->contentId);

        $items = $this->contentStore->findMany($this->contentId);
        if (count($items) === 0) {
            $this->items = $this->getFakeComponents();
            return;
        }

        foreach ($items as $item) {
            $this->items[] = new Map($item->contentId, $this->componentStore, $this->contentStore);
        }
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
        $columns = $component->getDecoration('columns')['columns'] ?? null;
        if ($columns === null) {
            // Use default columns
            $columns = $componentStore->whereParentKey(ComponentStandard::componentKeyFromContentId($contentId));
            $columns = array_filter($columns, static fn(ComponentEntity $column) => $column->type === 'text');
            $columns = array_slice($columns, 0, 4);
            $columns = array_map(static function (ComponentEntity $column) {
                $key = explode('/', $column->key);
                $key = end($key);
                return ['id' => $key, 'label' => $key];
            }, $columns);
        }
        $fields = array_map(static fn($column) => $column['id'], $columns);

        $data = $contentStore->whereIn($contentId, $fields, true);

        // Make rows by grouping on the id minus the relative id
        $rows = [];
        foreach ($data as $item) {
            // Ensure row exists even if there is no data
            if ($item->value === '__is_parent') {
                $rows[$item->contentId] = $rows[$item->contentId] ?? [];
                continue;
            }

            // Trim relative id
            $regex             = '/\/(?:' . implode('|', $fields) . ')$/';
            $parentId          = preg_replace($regex, '', $item->contentId, 1);
            $rows[$parentId][] = $item;
        }

        return [$columns, $rows];
    }

    private function getFakeComponents(): array
    {
        $component = $this->componentStore->find($this->componentKey);

        $max = $component->getDecoration('max')['value'] ?? 100;
        $min = $component->getDecoration('min')['value'] ?? 1;

        $amount = random_int($min, $max);

        $i     = 1;
        $items = [];
        while ($i <= $amount) {
            $i++;
            $items[] = new Map(
                $this->contentId,
                $this->componentStore,
                $this->contentStore,
            );
        }
        return $items;
    }
};
