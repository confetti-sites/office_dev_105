<?php

declare(strict_types=1);

namespace Confetti\Helpers;


use Confetti\Components\List_;
use Confetti\Components\Map;
use Confetti\Components\SelectFile;
use Exception;

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

    public static function query(): List_
    {
        throw new \RuntimeException('This method `query` should be overridden in the child class.');
    }

    public static function getComponentKey(): string
    {
        throw new \RuntimeException('This method `getComponentKey` should be overridden in the child class.');
    }

    /**
     * @internal This method is not part of the public API and should not be used.
     */
    protected static function getParamsForNewQuery(string $id): array
    {
        // We need to know where this method is called from so that we can store
        // it as a very specific small part in the advanced caching mechanism.
        // This allows us to replace a specific component in a large caching content.
        $location = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $as       = $location['file'] . ':' . $location['line'];
        // Get relative and parent from the key.
        $found    = preg_match('/(?<parent>.*)\/(?<relative>[^\/]*)$/', $id, $matches);
        $parent   = $found === 0 ? $id : $matches['parent'];
        $relative = $found === 0 ? '' : $matches['relative'];
        // We use $parent (not $key) to get the data in the join.
        // We do this because that is in line with how List_ handles the data.
        return [$parent, $relative, new ContentStore($parent, $as), $as];
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

    public static function componentById(string $id): Map|ComponentStandard|DeveloperActionRequiredException
    {
        $query = new QueryBuilder();
        $query->setOptions(['response_with_full_id' => true]);

        $parentIds = '';
        foreach (array_filter(explode('/', $id)) as $part) {
            $parentIds .= '/' . $part;
            $query->appendSelect($parentIds);
        }
        try {
            $values = $query->run()[0]['data'];
        } catch (\JsonException $e) {
            return new DeveloperActionRequiredException('Error gj5o498w4: can\'t decode options: ' . $e->getMessage());
        }
        $className = ComponentStandard::componentClassById($id, $values);
        if ($className instanceof DeveloperActionRequiredException) {
            throw $className;
        }
        return new $className;
    }

    /**
     * @param string[] $ids
     * @return Map[]|ComponentStandard[]|\Confetti\Helpers\DeveloperActionRequiredException
     */
    public static function componentsByIds(array $ids): array|DeveloperActionRequiredException
    {
        $query = new QueryBuilder();
        $query->setOptions(['response_with_full_id' => true]);
        foreach ($ids as $id) {
            $query->appendSelect($id);
        }
        try {
            $values = $query->run()[0]['data'];
        } catch (\JsonException $e) {
            return new DeveloperActionRequiredException('Error gj5o498h5: can\'t decode options: ' . $e->getMessage());
        }
        $result = [];
        foreach ($values as $id => $value) {
            $className = ComponentStandard::componentClassById($id, $values);
            if ($className instanceof DeveloperActionRequiredException) {
                throw $className;
            }
            $result[$id] = new $className;
        }
        return $result;
    }

    /**
     * @return class-string|\Confetti\Components\Map|ComponentStandard
     * @noinspection PhpDocSignatureInspection
     */
    private static function componentClassById(string $id, array $values): string|DeveloperActionRequiredException
    {
        // Remove id banner/image~0123456789 -> banner/image
        $class     = preg_replace('/~[A-Z0-9_]{10}/', '~', $id);
        $parts     = explode('/', $class);
        $isPointer = false;
        $result    = [];
        foreach ($parts as $part) {
            // Remove pointers banner/image~ -> banner/image_list
            if (str_ends_with($part, '~')) {
                $part = str_replace('~', '_list', $part);
            }
            // Remove pointers banner/template- -> banner/template
            if (str_ends_with($part, '-')) {
                $isPointer = true;
                $part      = str_replace('-', '_pointer', $part);
            }
            // Rename forbidden class names
            if (in_array($part, self::FORBIDDEN_PHP_KEYWORDS)) {
                $part .= '_';
            }
            $result[] = $part;
            // If a child is a pointer, we need a totally different class.
            if ($isPointer) {
                $className = implode('\\', $result);
                $extended  = self::getExtendedModelKey($className, $id, $values);
                if ($extended instanceof DeveloperActionRequiredException) {
                    return $extended;
                }
                $result    = explode('/', $extended);
                $isPointer = false;
            }
        }
        return implode('\\', $result);
    }

    abstract public function get(): mixed;

    public function __toString(): string
    {
        if ($this->contentStore === null) {
            throw new \RuntimeException("Component '{ComponentStandard::getComponent()->key}' is only used as a reference. Therefore, you can't convert `new {ComponentStandard::getComponent()->key}` to a string.");
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

    private static function getExtendedModelKey(string $pointerClassName, string $id, array $values): string|Exception
    {
        /** @var \Confetti\Components\SelectFile $pointer */
        $params  = self::getParamsForNewQuery($id);
        $pointer = new $pointerClassName(...$params);
        if (!array_key_exists($id, $values)) {
            return new DeveloperActionRequiredException("Can't find selected value with id '{$id}' in the values array. Please make sure that the selected value is set in the values array.");
        }
        // Get class and get the pointed file from the class
        $map = self::getExtendedModelByPointer($pointer, $values[$id]);
        if ($map instanceof DeveloperActionRequiredException) {
            return $map;
        }
        return $map->getComponent()->key;
    }

    private static function getExtendedModelByPointer(SelectFile $pointer, ?string $value): Map|Exception
    {
        $options = $pointer->getOptions();

        if ($value) {
            if (count($options) === 0) {
                return new DeveloperActionRequiredException("Selected value found to extend '{$pointer->getId()}'. But no options are set. Defined in '{$pointer->getComponent()->source}'");
            }
            if (!array_key_exists($value, $options)) {
                return new DeveloperActionRequiredException("Selected value found to extend '{$pointer->getId()}'. But file doesn't exist in the options list. Defined in '{$pointer->getComponent()->source}'");
            }
            return $options[$value];
        }
        // Get default value
        $file = $pointer->getComponent()->getDecoration('default');
        if ($file && array_key_exists($file, $options)) {
            return $options[$file];
        }
        // If no default value is set, use the first file in the list
        $file = $pointer->getComponent()->getDecoration('match', 'files')[0] ?? null;
        if ($file && array_key_exists($file, $options)) {
            return $options[$file];
        }
        return new DeveloperActionRequiredException("Can't found default value or first file in the list to extend '{$pointer->getId()}'. Make sure that there are options defined in '{$pointer->getComponent()->source}'");
    }
}
