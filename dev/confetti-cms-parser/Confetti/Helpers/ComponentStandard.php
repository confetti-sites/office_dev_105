<?php

declare(strict_types=1);

namespace Confetti\Helpers;

abstract class ComponentStandard
{
    private const FORBIDDEN_PHP_KEYWORDS = [
        "abstract",
        "and",
        "array",
        "as",
        "break",
        "callable",
        "case",
        "catch",
        "class",
        "clone",
        "const",
        "continue",
        "declare",
        "default",
        "die",
        "do",
        "echo",
        "else",
        "elseif",
        "empty",
        "enddeclare",
        "endfor",
        "endforeach",
        "endif",
        "endswitch",
        "endwhile",
        "eval",
        "exit",
        "extends",
        "final",
        "finally",
        "for",
        "foreach",
        "function",
        "global",
        "goto",
        "if",
        "implements",
        "include",
        "include_once",
        "instanceof",
        "insteadof",
        "interface",
        "isset",
        "list",
        "namespace",
        "new",
        "or",
        "print",
        "private",
        "protected",
        "public",
        "require",
        "require_once",
        "return",
        "static",
        "switch",
        "throw",
        "trait",
        "try",
        "unset",
        "use",
        "var",
        "while",
        "xor",
        "yield",
    ];

    private array $decorations = [];

    public function __construct(
        protected ?string       $parentContentId = null,
        protected ?string       $relativeContentId = null,
        // We use the reference because we want to init the rest of the content store
        protected ?ContentStore &$contentStore = null,
    )
    {
    }

    abstract public function getComponentType(): string;

    public static function getComponentKey(): string
    {
        throw new \RuntimeException('This method `getComponentKey` should be overridden in the child class.');
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
        $as = $location['file'] . ':' . $location['line'];
        // Get relative and parent from the key.
        $key = static::getComponentKey();
        $found = preg_match('/(?<parent>.*)\/(?<relative>[^\/]*)$/', $key,$matches);
        $parent = $found === 0 ? $key : $matches['parent'];
        $relative = $found === 0 ? '' : $matches['relative'];
        return [$parent, $relative, new ContentStore($key, $as), $as];
    }

    /**
     * When using the abstract component (\Confetti\Components\Text) we use this method.
     * The specific component (\model\homepage\feature\title) will override this method.
     */
    public function getComponent(): ComponentEntity
    {
        $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $dir      = dirname($location['file']);
        $file     = basename($location['file']);
        return new ComponentEntity(
            $this->getComponentKey(),
            $this->getComponentType(),
            $this->decorations,
            new SourceEntity(
                $dir,
                $file,
                $location['line'],
                0,
                0,
            ),
        );
    }

    public function getId(): string
    {
        return self::mergeIds($this->parentContentId, $this->relativeContentId);
    }

    public function getLabel(): string
    {
        $label = $this->getComponent()->getDecoration('label');
        if ($label) {
            return $label;
        }
        return titleByKey($this->getComponentKey());
    }

    public function getChildren(): array
    {
        return [];
    }

    public static function componentKeyFromContentId(string $contentId): string
    {
        return preg_replace('/~[A-Z0-9_]+/', '~', $contentId);
    }

    public static function componentClassByContentId(string $key, string $relativeId = null): string
    {
        if ($relativeId !== null) {
            $key = self::mergeIds($key, $relativeId);
        }
        // Remove id banner/image~0123456789 -> banner/image
        $class = preg_replace('/~[A-Z0-9_]{10}/', '~', $key);
        $parts  = explode('/', $class);
        $result = [];
        foreach ($parts as $part) {
            // Remove pointers banner/image~ -> banner/image_list
            if (str_ends_with($part, '~')) {
                $part = str_replace('~', '_list', $part);
            }
            // Remove pointers banner/template- -> banner/template
            if (str_ends_with($part, '-')) {
                $part  = substr($part, 0, -1);
                $part = str_replace('-', '_pointer', $part);
            }
            // Rename forbidden class names
            if (in_array($part, self::FORBIDDEN_PHP_KEYWORDS)) {
                $part .= '_';
            }
            $result[] = $part;
        }
        $class = implode('/', $result);

        // Replace Banner/Title with Banner\Title
        return str_replace('/', '\\', $class);
    }

    abstract public function get(): mixed;

    public function __toString(): string
    {
        if ($this->contentStore === null) {
            throw new \RuntimeException("Component '{$this->getComponent()->key}' is only used as a reference. Therefore, you can't convert `new {$this->getComponent()->key}` to a string.");
        }
        return (string) $this->get();
    }


    public static function mergeIds(string $parent, string $relative): string
    {
        if (str_starts_with($relative, '/')) {
            return $relative;
        }
        return $parent . '/' . $relative;
    }

    /**
     * @internal This method is not part of the public API and should not be used.
     */
    protected function getParamsForSelectedModel(): array
    {
        // We need to know where this method is called from so that we can store
        // it as a very specific small part in the advanced caching mechanism.
        // This allows us to replace a specific component in a large caching content.
        $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $as       = $location['file'] . ':' . $location['line'];

        // Parameters for the constructor of the child classes.
        return [$this->parentContentId, $this->relativeContentId, $this->contentStore, $as];
    }

    protected function setDecoration(string $key, mixed $value): void
    {
        $this->decorations[$key] = $value;
    }

    protected function decode(string $json): mixed
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return 'Error 7o8h5edg4n5jk: can\'t decode options: ' . $json . ', Message ' . $e->getMessage();
        }
    }
}
