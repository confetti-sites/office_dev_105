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
        $this->contentStore->join($this->relativeContentId, $as);
    }

    /**
     * @return \IteratorAggregate|Map[]
     */
    public function get(): IteratorAggregate
    {
        // Ensure that the content is initialized
        $this->contentStore->init($this->as);

        return new class($this->parentContentId, $this->relativeContentId, $this->componentStore, $this->contentStore, $this->as) implements IteratorAggregate {
            public function __construct(
                protected string         $parentContentId,
                protected string         $relativeContentId,
                protected ComponentStore $componentStore,
                protected ContentStore   $contentStore,
                protected string         $as,
            )
            {
            }

            public function getIterator(): Traversable
            {
                // Check if content is present
                $items = $this->contentStore->getContentOfThisLevel();

                // If data is present and useful, then we can use it
//                    if ($items !== null && !$firstEmptyContent) {
                if ($items !== null) {
                    $class = ComponentStandard::componentClassByContentId($this->parentContentId, $this->relativeContentId);
                    foreach ($items as $item) {
                        $childContentStore = clone $this->contentStore;
                        $childContentStore->setCurrentLevel($item['id']);
                        yield new $class($this->parentContentId, $item['id'], $this->componentStore, $childContentStore, $this->as);
                    }
                    return;
                }

                // $firstEmptyContent can be loaded due init
                // When the content is not present, we want to load all the data
                // But to prevent n+1 problem, we need to load the first item.
                $firstEmptyContent ??= $this->contentStore->findFirstOfJoin();
                if ($firstEmptyContent === null) {
                    throw new \Exception('null first empty content,  @todo implement');
                }
                if (count($firstEmptyContent) === 0) {
                    throw new \Exception('no first empty content,  @todo implement');
                }
                $class = ComponentStandard::componentClassByContentId($this->parentContentId, $this->relativeContentId);
                $childContentStore = clone $this->contentStore;
                $childContentStore->setCurrentLevel($firstEmptyContent[0]['id']);
                yield new $class($this->parentContentId, $firstEmptyContent[0]['id'], $this->componentStore, $childContentStore);

                // After the first item is loaded and cached, we can load the rest of the items in one go.
                $contents = $this->contentStore->findRestOfJoin();
                foreach ($contents as $content) {
                    $childContentStore = clone $this->contentStore;
                    $childContentStore->setCurrentLevel($content['id']);
                    yield  new $class($this->parentContentId, $content['id'], $this->componentStore, $childContentStore);
                }

//                // If ['data'] and ['join'] are empty, then this data is useless;
//                // because we want to fetch child content with cached queries
//                $firstEmptyContent = null;
//                if (!empty($items) && empty($items[0]['data']) && empty($items[0]['join'])) {
//                    $firstEmptyContent = $items[0];
//                }
//
//                // If data is present and useful, then we can use it
//                if ($items !== null && !$firstEmptyContent) {
//                    $class = ComponentStandard::componentClassByContentId($this->parentContentId, $this->relativeContentId);
//                    foreach ($items as $item) {
//                        $childContentStore = $this->contentStore;
//                        $childContentStore->setCurrentLevel($item['id']);
//                        yield new $class($this->parentContentId, $item['id'], $this->componentStore, $childContentStore, $this->as);
//                    }
//                    return;
//                }
//
//                // $firstEmptyContent can be loaded due init
//                // When the content is not present, we want to load all the data
//                // But to prevent n+1 problem, we need to load the first item.
//                $firstEmptyContent ??= $this->contentStore->findFirstOfJoin();
//                if ($firstEmptyContent === null) {
//                    return;
//                }
//                $class = ComponentStandard::componentClassByContentId($this->parentContentId, $this->relativeContentId);
//                $childContentStore = $this->contentStore;
//                $childContentStore->setCurrentLevel($firstEmptyContent['id']);
//                yield new $class($this->parentContentId, $firstEmptyContent['id'], $this->componentStore, $childContentStore);
//
//                // After the first item is loaded and cached, we can load the rest of the items in one go.
//                $contents = $this->contentStore->findRestOfJoin();
//                if (empty($contents)) {
//                    return;
//                }
//                foreach ($contents as $content) {
//                    $childContentStore = $this->contentStore;
//                    $childContentStore->setCurrentLevel($content['id']);
//                    yield  new $class($this->parentContentId, $content['id'], $this->componentStore, $childContentStore);
//                }
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
//        $component = $this->componentStore->find($this->getFullContentId());
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