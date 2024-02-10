<?php

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;

abstract class Map
{
    public function __construct(
        protected ?string         $contentId = null,
        protected ?ComponentStore $componentStore = null,
        protected ?ContentStore   $contentStore = null,
    )
    {
    }

    abstract public function getComponentKey(): string;

    public function getContentId(): string
    {
        return $this->contentId;
    }

    public function newRoot(string $contentId, string $as): self
    {
        $componentStore = new ComponentStore();
        $contentStore = new ContentStore($contentId, $as);
        return new static($contentId, $componentStore, $contentStore);
    }

    public function label(string $value): self
    {
        return $this;
    }

    public function color(string $key): Color
    {
        return new Color(
            "{$this->contentId}/{$key}",
            $this->componentStore,
            $this->contentStore,
        );
    }

    public function image(string $key): Image
    {
        return new Image(
            "{$this->contentId}/{$key}",
            $this->componentStore,
            $this->contentStore,
        );
    }

    public function list(string $key): List_
    {
        return new List_(
            "{$this->contentId}/{$key}",
            $this->componentStore,
            $this->contentStore,
        );
    }

    public function number(string $key): Number
    {
        return new Number(
            "{$this->contentId}/{$key}",
            $this->componentStore,
            $this->contentStore,
        );
    }

    public function select(string $key): Select
    {
        return new Select(
            "{$this->contentId}/{$key}",
            $this->componentStore,
            $this->contentStore,
        );
    }

    public function selectFiles(string $key): SelectFiles
    {
        return new SelectFiles(
            "{$this->contentId}/{$key}",
            $this->componentStore,
            $this->contentStore,
        );
    }

    public function text(string $key): Text
    {
        return new Text(
            "{$this->contentId}/{$key}",
            $this->componentStore,
            $this->contentStore,
        );
    }

}
