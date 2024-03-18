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

    public function makeFake(bool $makeFake = true): self
    {
        $this->contentStore->setFakeMaker($makeFake);
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
        $found    = preg_match('/(?<parent>.*)\/(?<relative>[^\/]*)$/', $key, $matches);
        $parent   = $found === 0 ? $key : $matches['parent'];
        $relative = $found === 0 ? '' : $matches['relative'];
        return [$parent, $relative, new ContentStore($key, $as), $as];
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
        $as       = $location['file'] . ':' . $location['line'];
        return new List_($this->getId() . '~', $key, $this->contentStore, $as);
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

    public function selectFile(string $key): SelectFile
    {
        $className = \Confetti\Helpers\ComponentStandard::componentClassByContentId($this->getId() . '/' . $key);
        return new $className(
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
