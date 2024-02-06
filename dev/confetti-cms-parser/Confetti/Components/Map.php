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
        protected ?ContentStore   $contentRepository = null,
    )
    {
    }

    abstract public function getComponentKey(): string;

    public function getContentId(): string
    {
        return $this->contentId;
    }

    public function new(
        string         $contentId,
        ComponentStore $componentStore,
        ContentStore   $contentRepository
    ): self
    {
        return new static($contentId, $componentStore, $contentRepository);
    }

    public function label(string $value): self
    {
        return $this;
    }

    public function color(string $key): Color
    {
        return new Color(
            "{$this->key}/{$key}",
            $this->componentStore,
            $this->contentRepository,
        );
    }

    public function image(string $key): Image
    {
        return new Image(
            "{$this->key}/{$key}",
            $this->componentStore,
            $this->contentRepository,
        );
    }

    public function list(string $key): List_
    {
        return new List_(
            "{$this->key}/{$key}",
            $this->componentStore,
            $this->contentRepository,
        );
    }

    public function number(string $key): Number
    {
        return new Number(
            "{$this->key}/{$key}",
            $this->componentStore,
            $this->contentRepository,
        );
    }

    public function select(string $key): Select
    {
        return new Select(
            "{$this->key}/{$key}",
            $this->componentStore,
            $this->contentRepository,
        );
    }

    public function selectFiles(string $key): SelectFiles
    {
        return new SelectFiles(
            "{$this->key}/{$key}",
            $this->componentStore,
            $this->contentRepository,
        );
    }

    public function text(string $key): Text
    {
        return new Text(
            "{$this->key}/{$key}",
            $this->componentStore,
            $this->contentRepository,
        );
    }

}
