<?php

declare(strict_types=1);

namespace Confetti\Helpers;

abstract class ComponentStandard
{
    // Component key
    protected string $componentKey;

    public function __construct(
        protected ?string         $contentId = null,
        protected ?ComponentStore $componentStore = null,
        // We use the reference because we want to init the rest of the content store
        protected ?ContentStore   &$contentStore = null,
    )
    {
        $this->componentKey = self::componentKeyFromContentId($contentId);
    }

    public static function componentKeyFromContentId(string $contentId): string
    {
        return preg_replace('/~[A-Z0-9_]+/', '~', $contentId);
    }

    abstract public function get(): mixed;


    public function __toString(): string
    {
        return (string)$this->get();
    }
}
