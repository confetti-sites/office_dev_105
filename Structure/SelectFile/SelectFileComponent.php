<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Structure\SelectFile;

use Confetti\Components\FilePatternArray;
use Confetti\Components\Map;
use Confetti\Contracts\SelectFileInterface;
use Confetti\Contracts\SelectModelInterface;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ContentStore;

class SelectFileComponent extends ComponentStandard implements SelectModelInterface, SelectFileInterface
{
    public function type(): string
    {
        return 'selectFile';
    }

    public function __construct(string $parentContentId, string $relativeContentId, ContentStore &$contentStore)
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
        // Get saved value
        return $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
    }

    public function getView(): ?string
    {
        $file = $this->get();
        if (!str_ends_with($file, '.blade.php')) {
            return null;
        }
        $file = str_replace('.blade.php', '', $file);
        return str_replace('/', '.', $file);
    }

    /**
     * @return \Confetti\Components\Map[]
     */
    public function getOptions(): array
    {
        throw new \RuntimeException('This method `getOptions` should be overridden in the child class.');
    }

    public function getSelected(): ?Map
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
        return 'admin.structure.select_file.input';
    }

    /**
     * The return value is a full path from the root to a mjs file.
     */
    public static function getViewAdminListItemMjs(): string
    {
        return '/admin/structure/select_file/list_item.mjs';
    }

    // Label is used as a title for the admin panel
    public function label(string $label): self
    {
        // The arguments must be hardcoded, any additional
        // values can only be used within this class.
        return $this;
    }

    // Default value is used when the user hasn't saved any value
    public function default(string $default): self
    {
        // The arguments must be hardcoded, any additional
        // values can only be used within this class.
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

    /**
     * We can save the label of the selected file in a (hidden) field.
     * Then we can use this label in the admin to show in the list.
     * Please do not remove this method since it is used in the '#useLabelFor()' method.
     */
    public function useLabelFor(string $useLabelFor): self
    {
        // The arguments must be hardcoded, any additional
        // values can only be used within this class.
        return $this;
    }
}



