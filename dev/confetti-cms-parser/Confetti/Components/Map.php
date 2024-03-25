<?php

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentEntity;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ContentStore;
use Confetti\Helpers\DeveloperActionRequiredException;

class Map
{
    public function __construct(
        protected ?string       $parentContentId = null,
        protected ?string       $relativeContentId = null,
        protected ?ContentStore $contentStore = null,
    )
    {
    }

    public static function getComponentKey(): string
    {
        throw new \RuntimeException('This method `getComponentKey` should be overridden in the child class.');
    }

    public function canFake(bool $canFake = true): self
    {
        $this->contentStore->setCanFake($canFake);
        return $this;
    }

    public function getId(): string
    {
        return ComponentStandard::mergeIds($this->parentContentId, $this->relativeContentId);
    }

    public function getComponent(): ComponentEntity
    {
        throw new \RuntimeException('This method `getComponent` should be overridden in the child class.');
    }

    public function getStore(): ContentStore
    {
        return $this->contentStore;
    }

    /**
     * @internal This method is not part of the public API and should not be used.
     */
    public function newRoot(string $contentId, string $as): self
    {
        $contentStore = new ContentStore($contentId, $as);
        return new static("", $contentId, $contentStore);
    }

    public function getLabel(): string
    {
        $component = $this->getComponent();
        $label     = $component->getDecoration('label');
        if ($label) {
            return $label;
        }
        return titleByKey($component->key);
    }

    /**
     * @return \Confetti\Components\Map[]|\Confetti\Components\List_[]
     */
    public function getChildren(): array
    {
        // Normally, this method should be overridden in the child class.
        // As situations may be:
        // 1. Create a file with ->list('blogs')->get()
        // 2. Use ->blogs()->get() in another file.
        // 3. Remove ->list('blogs')->get()
        return [];
    }

    /**
     * @internal This method is not part of the public API and should not be used.
     */
    protected static function getParamsForNewQuery(): array
    {
        // We need to know where this method is called from so that we can store
        // it as a very specific small part in the advanced caching mechanism.
        // This allows us to replace a specific component in a large caching content.
        $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $as       = $location['file'] . ':' . $location['line'];
        // Get relative and parent from the key.
        $key      = static::getComponentKey();
        [$parent, $relative] = ComponentStandard::explodeKey($key);
        return [$parent, $relative, new ContentStore($key, $as), $as];
    }

    /**
     * @internal This method is not part of the public API and should not be used.
     */
    protected function getParamsForProperty(string $key): array
    {
        $store = clone $this->contentStore;
        if (str_ends_with($key, '-')) {
            $store->joinPointer($key);
        }
        // Parameters for the constructor of the child classes.
        return [$this->getId(), $key, $store];
    }

    /**
     * @internal This method is not part of the public API and should not be used.
     */
    protected function getParamsForList(string $key): array
    {
        // We need to know where this method is called from so that we can store
        // it as a very specific small part in the advanced caching mechanism.
        // This allows us to replace a specific component in a large caching content.
        $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $as       = $location['file'] . ':' . $location['line'];

        // Parameters for the constructor of the child classes.
        return [$this->getId(), $key . '~', $this->contentStore, $as];
    }

    protected function decode(string $json): mixed
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return 'Error 7o8h5edg4n5jk: can\'t decode options: ' . $json . ', Message ' . $e->getMessage();
        }
    }

    public function label(string $value): self
    {
        return $this;
    }

    public function color(string $key): Map|Color
    {
        return $this->getComponentByRelativeId($key);
    }

    public function image(string $key): Map|Image
    {
        return $this->getComponentByRelativeId($key);
    }

    public function list(string $key): List_
    {
        exit('this is probably not needed and can be abstract');
        $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        $as       = $location['file'] . ':' . $location['line'];
        return new List_($this->getId() . '~', $key, $this->contentStore, $as);

//        $className = ComponentStandard::componentById($this->getId() . '~/' . $key);
//        if ($className instanceof DeveloperActionRequiredException) {
//            throw $className;
//        }
//        return new $className(
//            $this->getId() . '~',
//            $key,
//            $this->contentStore,
//            $as,
//        );
    }

    public function number(string $key): Map|Number
    {
        return $this->getComponentByRelativeId($key);
    }

    public function select(string $key): Map|Select
    {
        return $this->getComponentByRelativeId($key);
    }

    public function selectFile(string $key): Map|SelectFile
    {
        return $this->getComponentByRelativeId($key . '-');
    }

    public function text(string $key): Map|Text
    {
        return $this->getComponentByRelativeId($key);
    }

    private function getComponentByRelativeId(string $relativeId): Map|ComponentStandard
    {
        $className = ComponentStandard::componentClassById(
            $this->getId() . '/' . $relativeId,
            $this->contentStore
        );
        if ($className instanceof DeveloperActionRequiredException) {
            throw $className;
        }
        return new $className(
            $this->getId(),
            $relativeId,
            $this->contentStore,
        );
    }
}
