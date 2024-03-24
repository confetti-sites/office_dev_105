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

    /**
     * @param string[] $ids
     * @return string[]|Map[]|ComponentStandard[]|\Confetti\Helpers\DeveloperActionRequiredException
     */
    public static function componentClassNamesByIds(array $ids, ContentStore $store): array|DeveloperActionRequiredException
    {
        if (empty($ids)) {
            return [];
        }
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
            $className = ComponentStandard::componentClassById($id, $store);
            if ($className instanceof DeveloperActionRequiredException) {
                throw $className;
            }
            $result[$id] = $className;
        }
        return $result;
    }

    /**
     * @return class-string|\Confetti\Components\Map|ComponentStandard
     * @noinspection PhpDocSignatureInspection
     */
    public static function componentClassById(string $id, ContentStore &$store): string|DeveloperActionRequiredException
    {
        $pointerValues = self::getPointerValues($id, $store);

        // Remove id banner/image~0123456789 -> banner/image
        $class     = preg_replace('/~[A-Z0-9_]{10}/', '~', $id);
        $parts     = explode('/', ltrim($class, '/'));
        $pointerId = null;
        $result    = [];
        $idSoFar   = '';
        foreach ($parts as $part) {
            // If the parent is a pointer, the child needs a totally different class.
            if ($pointerId) {
                $className = '\\' . implode('\\', $result);
                $extended  = self::getExtendedModelKey($className, $idSoFar, $pointerValues);
                if ($extended instanceof DeveloperActionRequiredException) {
                    return $extended;
                }
                $result    = explode('\\', get_class($extended));
                $pointerId = null;
            }
            $classPart = $part;
            // Remove pointers banner/image~ -> banner/image_list
            if (str_ends_with($classPart, '~')) {
                $classPart = str_replace('~', '_list', $part);
            }
            // Remove pointers banner/template- -> banner/template
            if (str_ends_with($classPart, '-')) {
                $pointerId = $part;
                $classPart = str_replace('-', '_pointer', $classPart);
            }
            // Rename forbidden class names
            if (in_array($classPart, self::FORBIDDEN_PHP_KEYWORDS)) {
                $classPart .= '_';
            }
            $result[] = $classPart;
            $idSoFar .= '/' . $part;
        }
        return '\\' . implode('\\', $result);
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

    private static function getExtendedModelKey(string $pointerClassName, string $id, array $pointerValues): Map|Exception
    {
        $value = $pointerValues[$id] ?? null;

        /** @var \Confetti\Components\SelectFile $pointer */
        $params  = self::getParamsForNewQuery($id);
        $pointer = new $pointerClassName(...$params);
        // Get class and get the pointed file from the class
        return self::getExtendedModelByPointer($pointer, $value);
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

    private static function getPointerValues(string $id, ContentStore &$store): array
    {
        $allAlreadySelected = true;
        $result = [];
        $parts = explode('/', ltrim($id, '/'));
        $idSoFar = '';
        $content = $store->getContent();
        foreach ($parts as $part) {
            $idSoFar .= '/' . $part;
            // We can only add result here if the pointer is already selected
            // We only need to get the pointer values
            if (!empty($content['data']) && array_key_exists($idSoFar, $content['data']) && str_ends_with($part, '-')) {
                $result[$idSoFar] = $content['data'][$idSoFar];
                continue;
            }
            $allAlreadySelected = false;
            if (str_ends_with($part, '-')) {
                $store->selectInRoot($idSoFar);
            }
        }
        if ($allAlreadySelected) {
            exit('@todo; when exit here, it is ok. @todo when never exit here, it is not ok.');
            return $result;
//        } else {
            // second time exit
//            static $nr = 0;
//            $nr++;
//            if ($nr === 3) {
//                echo '<pre>';
//                var_dump($store->getContent());
//                echo '</pre>';
//                exit('Cached expected: ' . __FILE__ . ':' . __LINE__);
//            }
        }

        $store->runCurrentQuery([
            'use_cache'               => true,
            'patch_cache_select'      => true,
            'response_with_condition' => false,
            'use_cache_from_root'     => true, // We want all the data. Even if it is for the parent.
        ]);
        $content = $store->getContent();
        $idSoFar = '';
        foreach ($parts as $part) {
            $idSoFar .= '/' . $part;
            // We are only interested in the pointer values
            if (str_ends_with($part, '-')) {
                $result[$idSoFar] = $content['data'][$idSoFar];
            }
        }
        return $result;
    }
}
