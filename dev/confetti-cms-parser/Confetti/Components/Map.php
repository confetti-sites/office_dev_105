<?php

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentEntity;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ContentStore;
use Confetti\Helpers\SourceEntity;

class Map
{
    public function __construct(
        protected ?string         $parentContentId = null,
        protected ?string         $relativeContentId = null,
        protected ?ContentStore   $contentStore = null,
    )
    {
    }

    public function getId(): string
    {
        return ComponentStandard::mergeIds($this->parentContentId, $this->relativeContentId);
    }

    public function getComponent(): ComponentEntity
    {
        throw new \RuntimeException('This method should be overridden in the child class.');
    }

    /**
     * @internal This method is not part of the public API and should not be used.
     */
    public function newRoot(string $contentId, string $as): self
    {
        $contentStore = new ContentStore($contentId, $as);
        return new static("", $contentId, $contentStore);
    }

    public function guessLabel(): string
    {
        $component = $this->getComponent();
        $label = $component->getDecoration('label');
        if ($label) {
            return $label;
        }
        return titleByKey($component->key);
    }

    public function getChildren(): array
    {
        return [];
    }

    /**
     * @internal This method is not part of the public API and should not be used.
     */
    protected function getParamsForProperty(string $key): array
    {
        // Parameters for the constructor of the child classes.
        return [$this->getId(), $key, $this->contentStore];
    }

    /**
     * @internal This method is not part of the public API and should not be used.
     */
    protected function getParamsForChild(string $key): array
    {
        // We need to know where this method is called from so that we can store
        // it as a very specific small part in the advanced caching mechanism.
        // This allows us to replace a specific component in a large caching content.
        $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $as = $location['file'] . ':' . $location['line'];

        // Parameters for the constructor of the child classes.
        return [$this->getId(), $key, $this->contentStore, $as];
    }

    public function label(string $value): self
    {
        return $this;
    }

    public function color(string $key): Color
    {
        return new Color(
            $this->getId(),
            $key,
            $this->contentStore,
        );
    }

    public function image(string $key): Image
    {
        $result = new Image(
            $this->getId(),
            $key,
            $this->contentStore,
        );
        return $result;
    }

    public function list(string $key): List_
    {
        $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        $as = $location['file'] . ':' . $location['line'];
        return new List_($this->getId(), $key, $this->contentStore, $as);
    }

    public function number(string $key): Number
    {
        return new Number(
            $this->getId(),
            $key,
            $this->contentStore,
        );
    }

    public function select(string $key): Select
    {
        return new Select(
            $this->getId(),
            $key,
            $this->contentStore,
        );
    }

    public function selectFiles(string $key): SelectFile
    {
        return new SelectFile(
            $this->getId(),
            $key,
            $this->contentStore,
        );
    }

    public function text(string $key): Text
    {
        return new Text(
            $this->getId(),
            $key,
            $this->contentStore,
        );
    }

}
