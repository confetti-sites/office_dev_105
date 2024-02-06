<?php

declare(strict_types=1);

namespace Confetti\Helpers;


class ComponentStore
{
    /**
     * @var ComponentEntity[]
     */
    private array $components = [];

    public function __construct(array $params = [])
    {
        try {
            $this->components = self::fetchComponents($params);
        } catch (\JsonException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public static function newWherePrefix(string $key): self
    {
        return new self(['key_prefix', '/']);
    }

    public function find(string $key): ComponentEntity
    {
        if (!isset($this->components[$key])) {
            throw new \RuntimeException("Component {$key} not found");
        }

        return $this->components[$key];
    }

    public function findOrNull(string $key): ?ComponentEntity
    {
        return $this->components[$key] ?? null;
    }

    /**
     * @return \Confetti\Helpers\ComponentEntity[]
     */
    public function whereParentKey(string $key): array
    {
        $components = [];
        foreach ($this->components as $component) {
            if ($component->parentKey === $key) {
                $components[] = $component;
            }
        }
        return $components;
    }

    /**
     * @return \Confetti\Helpers\ComponentEntity[]
     */
    public function whereType(string $type): array
    {
        $components = [];
        foreach ($this->components as $component) {
            if ($component->type === $type) {
                $components[] = $component;
            }
        }
        return $components;
    }

    /**
     * @param string $pattern A glob pattern.
     *
     * The ? matches 1 of any character except a /
     * The * matches 0 or more of any character except a /
     * The ** matches 0 or more of any character including a /
     * The [abc] matches 1 of any character in the set
     * The [!abc] matches 1 of any character not in the set
     * The [a-z] matches 1 of any character in the range
     *
     * Examples: *.css /templates/**.css
     *
     * See http://www.jedit.org/users-guide/globs.html for more examples.
     *
     * @return \Confetti\Helpers\ComponentEntity[]
     */
    public function whereMatch(string $pattern): array
    {
        $components = [];
        foreach ($this->components as $component) {
            if (fnmatch($pattern, $component->key, FNM_PATHNAME | FNM_CASEFOLD)) {
                $components[] = $component;
            }
        }
        return $components;
    }

    /**
     * @return \Confetti\Helpers\ComponentEntity[]
     */
    public function all(): array
    {
        return $this->components;
    }

    /**
     * @return \Confetti\Helpers\ComponentEntity[]
     */
    public function toArray(): array
    {
        return $this->components;
    }

    private static function fetchComponents(array $params): array
    {
        $client = new Client();
        $query = http_build_query($params);
        $response = $client->get('confetti-cms-structure:80/components?' . $query);
        $components = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        $result = [];
        foreach ($components as $component) {
            $result[$component['key']] = new ComponentEntity(
                key: $component['key'],
                type: $component['type'],
                parentKey: $component['parent_key'],
                decorations: self::getDecorations($component['decorations']),
                source: new SourceEntity(
                    directory: $component['source']['directory'],
                    file: $component['source']['file'],
                    line: $component['source']['line'],
                    from: $component['source']['from'],
                    to: $component['source']['to'],
                )
            );
        }
        return $result;
    }

    private static function getDecorations($decorations1): array
    {
        $decorations = [];
        foreach ($decorations1 as $decoration) {
            $decorations[] = new DecorationEntity(
                type: $decoration['type'],
                data: $decoration['data'],
            );
        }
        return $decorations;
    }
}