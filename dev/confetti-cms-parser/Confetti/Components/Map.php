<?php

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;

class Map
{
    public function __construct(
        protected ?string         $parentContentId = null,
        protected ?string         $relativeContentId = null,
        protected ?ComponentStore $componentStore = null,
        protected ?ContentStore   $contentStore = null,
    )
    {
    }

    public function getFullId(): string
    {
        return ComponentStandard::mergeIds($this->parentContentId, $this->relativeContentId);
    }

    public function newRoot(string $contentId, string $as): self
    {
        $componentStore = new ComponentStore();
        $contentStore = new ContentStore($contentId, $as);
        return new static("", $contentId, $componentStore, $contentStore);
    }

    protected function getParamsForProperty(string $key): array
    {
        // Parameters for the constructor of the child classes.
        return [$this->getFullId(), $key, $this->componentStore, $this->contentStore];
    }

    protected function getParamsForChild(string $key): array
    {
        // We need to know where this method is called from so that we can store
        // it as a very specific small part in the advanced caching mechanism.
        // This allows us to replace a specific component in a large caching content.
        $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $as = $location['file'] . ':' . $location['line'];

        // Parameters for the constructor of the child classes.
        return [$this->getFullId(), $key, $this->componentStore, $this->contentStore, $as];
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
