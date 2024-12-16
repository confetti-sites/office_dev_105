<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Components;

use Confetti\Components\FilePatternArray;
use Confetti\Components\Map;
use Confetti\Contracts\SelectFileInterface;
use Confetti\Contracts\SelectModelInterface;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ContentStore;
use Confetti\Model\RawFile;

class SelectFileComponent extends ComponentStandard implements SelectModelInterface, SelectFileInterface
{
    public function type(): string
    {
        return 'selectFile';
    }

    public function __construct(string $parentContentId, string $relativeContentId, ContentStore $contentStore)
    {
        if ($relativeContentId != null && !str_ends_with($relativeContentId, '-')) {
            $relativeContentId .= '-';
        }
        parent::__construct($parentContentId, $relativeContentId, $contentStore);
        $this->contentStore = clone $this->contentStore;
        $this->contentStore->joinPointer($this->relativeContentId);
    }

    public function get(): ?string
    {
        // For now, we do not allow to select itself. Then we create a recursive 'from' value. For example:
        // /model/feature/feature~2HEF1WN1HS/type-/value-/value-/value-/value-/value-/value-/value-/value-/
        // count slashes and throw an exception if the count is higher than 20
        if (substr_count($this->parentContentId . '/' . $this->relativeContentId, '/') > 20) {
            throw new \RuntimeException("Recursive structure detected. For now, we do not allow SelectFileComponent to select with the same selection as it is in. Current selection: $this->parentContentId/$this->relativeContentId");
        }

        // Get saved value
        return $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
    }

    public function getView(): ?string
    {
        $file = $this->get();
        if (!str_ends_with($file, '.blade.php')) {
            if ($file === null) {
                return null;
            }
            return $file;
        }
        $file = str_replace('.blade.php', '', $file);
        $file = ltrim($file, '/');
        return str_replace('/', '.', $file);
    }

    /**
     * @return \Confetti\Components\Map[]|\Confetti\Model\RawFile[]
     */
    public function getOptions(): array
    {
        throw new \RuntimeException('This method `getOptions` should be overridden in the child class.');
    }

    /**
     * @return Map|RawFile|null Return type is mixed because the return value will be narrowed down in the parent class.
     */
    public function getSelected(): mixed
    {
        $file = self::getPointerValues($this->getId(), $this->contentStore)[$this->getId()] ?? null;

        // Get default value
        if ($file === null) {
            $component = $this->getComponent();
            $file      = $component->getDecoration('default');
        }

        // If no default value is set, use the first file in the list
        if ($file === null) {
            $file = array_key_first($this->getOptions());
        }

        return $this->getOptions()[$file] ?? null;
    }

    /**
     * The return value is a full path from the root to a blade file.
     */
    public function getViewAdminInput(): string
    {
        return 'admin.components.select_file.input';
    }

    /**
     * The return value is a full path from the root to a mjs file.
     */
    public static function getViewAdminPreview(): string
    {
        return '/admin/components/select_file/preview.mjs';
    }

    // Label is used as a title for the admin panel
    public function label(string $label): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    // Default value is used when the user hasn't saved any value
    public function default(string $default): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    // List all files by directories. You can use the glob pattern. For example, `->match(['/view/footers'])`
    //
    // @param string $pattern A glob pattern.
    //
    // The ? matches 1 of any character except a /
    // The * matches 0 or more of any character except a /
    // The ** matches 0 or more of any character including a /
    // The [abc] matches 1 of any character in the set
    // The [!abc] matches 1 of any character not in the set
    // The [a-z] matches 1 of any character in the range
    //
    // Example: ['*.css', '/templates/**.css']
    //
    // Note:
    // Do not change the name, type or the first parameter name
    // of this method. The 'structure' service expects this method.
    // public function match(#[FilePatternArray] array $matches): self
    //                 ^^^^^^^^^^^^^^^^^^^^^^^^^ ^^^^^ ^^^^^^^^
    public function match(#[FilePatternArray] array $matches): self
    {
        $this->setDecoration(__FUNCTION__, [
            "patterns" => $matches,
            "files"    => null, // This will be filled by the 'parser' service
        ]);
        return $this;
    }

    // Required removes the "Nothing selected" option.
    public function required(): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    /**
     * We can save the label of the selected file in a (hidden) field.
     * Then we can use this label in the admin to show in the list.
     * Please do not remove this method since it is used in the '#useLabelFor()' method.
     */
    public function useLabelFor(string $useLabelFor): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }
}



