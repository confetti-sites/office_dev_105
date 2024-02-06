<?php

declare(strict_types=1);

namespace Confetti\Helpers;

class ComponentEntity
{
    /**
     * @param \Confetti\Helpers\DecorationEntity[] $decorations
     */
    public function __construct(
        public readonly string       $key,
        public readonly string       $type,
        public readonly string       $parentKey,
        public readonly array        $decorations,
        public readonly SourceEntity $source,
    )
    {

    }

    public function hasDecoration(string $string): bool
    {
        foreach ($this->decorations as $decoration) {
            if ($decoration->type === $string) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getDecoration(string $string): ?array
    {
        foreach ($this->decorations as $decoration) {
            if ($decoration->type === $string) {
                return $decoration->data;
            }
        }
        return null;
    }

    public function dumpDecorations(): void
    {
        foreach ($this->decorations as $decoration) {
            echo '<pre>' . var_export($decoration->type, true) . '</pre>';
            echo '<pre>' . var_export($decoration->data, true) . '</pre>';
        }
    }
}
