<?php

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;

abstract class Map
{
    public function __construct(
        protected ?string         $parentContentId = null,
        protected ?string         $relativeContentId = null,
        protected ?ComponentStore $componentStore = null,
        protected ?ContentStore   $contentStore = null,
    )
    {
    }

    abstract public function getComponentKey(): string;

    public function getFullId(): string
    {
        if ($this->relativeContentId === null) {
            echo '<pre>';
            var_dump($this->parentContentId);
            echo '</pre>';
            exit('debug getFullId');
        }
        return ComponentStandard::mergeIds($this->parentContentId, $this->relativeContentId);
    }

    public function newRoot(string $contentId, string $as): self
    {
        $componentStore = new ComponentStore();
        $contentStore = new ContentStore($contentId, $as);
        return new static("", $contentId, $componentStore, $contentStore);
    }

    public function label(string $value): self
    {
        return $this;
    }

    public function color(string $key): Color
    {
        return new Color(
            $this->getFullId(),
            $key,
            $this->componentStore,
            $this->contentStore,
        );
    }

    public function image(string $key): Image
    {
        return new Image(
            $this->getFullId(),
            $key,
            $this->componentStore,
            $this->contentStore,
        );
    }

    public function list(string $key): List_
    {
        $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        $as = $location['file'] . ':' . $location['line'];
        return new List_($this->getFullId(), $key, $this->componentStore, $this->contentStore, $as);
    }

    public function number(string $key): Number
    {
        return new Number(
            $this->getFullId(),
            $key,
            $this->componentStore,
            $this->contentStore,
        );
    }

    public function select(string $key): Select
    {
        return new Select(
            $this->getFullId(),
            $key,
            $this->componentStore,
            $this->contentStore,
        );
    }

    public function selectFiles(string $key): SelectFiles
    {
        return new SelectFiles(
            $this->getFullId(),
            $key,
            $this->componentStore,
            $this->contentStore,
        );
    }

    public function text(string $key): Text
    {
        return new Text(
            $this->getFullId(),
            $key,
            $this->componentStore,
            $this->contentStore,
        );
    }

}
