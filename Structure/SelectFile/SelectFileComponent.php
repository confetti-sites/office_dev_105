<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Structure\SelectFile;

use Confetti\Components\FilePatternArray;
use Confetti\Components\Map;
use Confetti\Contracts\SelectFileInterface;
use Confetti\Contracts\SelectModelInterface;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ContentStore;

abstract class SelectFileComponent extends ComponentStandard implements SelectModelInterface, SelectFileInterface
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

    public function get(): string
    {
        // Get saved value
        $filePath = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($filePath !== null) {
            return $filePath;
        }
        $component = $this->getComponent();

        // Get default view
        $filePath = $component->getDecoration('default');
        if ($filePath === null) {
            return '';
        }
        return $filePath;
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

    public function getViewAdminInput(): string
    {
        return 'structure.selectfile.input';
    }

    public function getViewAdminListItem(): string
    {
        return 'structure.SelectFile.list_item';
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
}



