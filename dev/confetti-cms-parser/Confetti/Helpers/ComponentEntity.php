<?php

declare(strict_types=1);

namespace Confetti\Helpers;

class ComponentEntity
{
    public function __construct(
        public readonly string       $key,
        public readonly string       $type,
        public readonly array        $decorations,
        public readonly SourceEntity $source,
    )
    {

    }

    public function hasDecoration(string $key): bool
    {
        if ($this->getDecoration($key) !== null) {
            return true;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getDecorations(): array
    {
        return $this->decorations;
    }

    /**
     * @param string|null $parameter to search deep, you can use dot notation: `size.min`
     */
    public function getDecoration(string $method, ?string $parameter = null): mixed
    {
        $data = $this->decorations[$method] ?? null;
        if ($data === null) {
            return null;
        }

        // If no parameter is given, and no parameter is given, and the data is an array,
        // the value is not a value (for example, an boolean or an integer). In that case,
        // we use are assumed that the parameter name is the same as the method name.
        if ($parameter === null && is_array($data) && array_key_exists($method, $data)) {
            return $data[$method] ?? null;
        }

        // Search with parameter
        $parameters = explode('.', $parameter ?? '');
        foreach ($parameters as $parameter) {
            if (isset($data[$parameter])) {
                $data = $data[$parameter];
            } else {
                return null;
            }
        }
        return $data;
    }

    public function dumpDecorations(): void
    {
        foreach ($this->decorations as $decoration) {
            echo '<pre>' . var_export($decoration->type, true) . '</pre>';
            echo '<pre>' . var_export($decoration->data, true) . '</pre>';
        }
    }
}
