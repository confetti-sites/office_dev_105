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
        protected ?string         $parentContentId = null,
        protected ?string         $relativeContentId = null,
        // We use the reference because we want to init the rest of the content store
        protected ?ContentStore   &$contentStore = null,
    )
    {
    }

    abstract public function getComponentType(): string;

    /**
     * When using the abstract component (\Confetti\Components\Text) we use this method.
     * The specific component (\model\homepage\feature\title) will override this method.
     */
    public function getComponent(): ComponentEntity
    {
        $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $dir = dirname($location['file']);
        $file = basename($location['file']);
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

    public function getComponentKey(): string
    {
        // When using the abstract component (\Confetti\Components\Text) we use this method.
        // The specific component (\model\homepage\feature\title) will override this method.
        return static::componentKeyFromContentId($this->getFullContentId());
    }

    public function getFullContentId(): string
    {
        return self::mergeIds($this->parentContentId, $this->relativeContentId);
    }

    public function setDecoration(string $key, mixed $value): void
    {
        $this->decorations[$key] = $value;
    }

    public static function componentKeyFromContentId(string $contentId): string
    {
        return preg_replace('/~[A-Z0-9_]+/', '~', $contentId);
    }

    public static function componentClassByContentId(string $parentId, string $relativeId): string
    {
        $key = self::mergeIds($parentId, $relativeId);
        // Remove id banner/image~0123456789 -> banner/image
        $class = preg_replace('/~[A-Z0-9_]{10}/', '', $key);

        // Remove pointers banner/image~ -> banner/image
        $class = str_replace('~', '', $class);

        // Remove pointers banner/template- -> banner/template
        $class = str_replace('-', '', $class);

        // Rename forbidden class names
        $parts  = explode('/', $class);
        $result = [];
        foreach ($parts as $part) {
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
}
