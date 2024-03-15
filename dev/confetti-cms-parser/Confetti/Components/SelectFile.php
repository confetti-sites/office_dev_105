<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Contracts\SelectFileInterface;
use Confetti\Contracts\SelectModelInterface;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ContentStore;

class SelectFile extends ComponentStandard implements SelectModelInterface, SelectFileInterface
{
    public function __construct(?string $parentContentId = null, ?string $relativeContentId = null, ?ContentStore &$contentStore = null)
    {
        if ($relativeContentId != null && !str_ends_with($relativeContentId, '-')) {
            $relativeContentId .= '-';
        }
        parent::__construct($parentContentId, $relativeContentId, $contentStore);
    }

    public function get(): string
    {
        // Get saved value
        $filePath = $this->contentStore->findOneData($this->getId());
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

    public function getComponentType(): string
    {
        return 'selectFile';
    }

    public function getSelected(): Map
    {
        throw new \RuntimeException('This method `getSelected` should be overridden in the child class.');
    }

    /**
     * @return \Confetti\Components\Map[]
     */
    public function getOptions(): array
    {
        throw new \RuntimeException('This method `getOptions` should be overridden in the child class.');
    }

    // List all files by directories. You can use the glob pattern. For example: `->match(['/view/footers'])`
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
    // Examples: *.css /templates/**.css
    public function match(array $matches): self
    {
        $this->setDecoration('match', [
            'matches' => $matches,
        ]);
        return $this;
    }

    // Before saving this will be the default file. With match, the file must be in the directory.
    public function default(string $default): self
    {
        $this->setDecoration('default', [
            'default' => $default,
        ]);
        return $this;
    }

    // Label is used as a field title in the admin panel
    public function label(string $label): self
    {
        $this->setDecoration('label', [
            'label' => $label,
        ]);
        return $this;
    }
}