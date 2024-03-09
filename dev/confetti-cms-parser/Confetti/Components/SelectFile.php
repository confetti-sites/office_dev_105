<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

class SelectFile extends ComponentStandard implements \Confetti\Contracts\SelectModelInterface
{
    public function get(): string
    {
        exit('asdfadsfdsf');
        // Get saved value
        $filePath = $this->contentStore->findOneData($this->getId());
        if ($filePath !== null) {
            if (str_ends_with($filePath, '.blade.php')) {
                return self::getViewByPath($filePath);
            }
            return $filePath;
        }
        $component = $this->getComponent();

        // Get default view
        $filePath = $component->getDecoration('default');
        if ($filePath === null) {
            return '';
        }
        if (str_ends_with($filePath, '.blade.php')) {
            echo '<pre>';
            var_dump($filePath);
            echo '</pre>';
            exit('debug asdf');
            return self::getViewByPath($filePath);
        }
        return $filePath;
    }

    private static function getViewByPath(string $path): string
    {
        $path = str_replace('.blade.php', '', $path);
        return str_replace('/', '.', $path);
    }

    public function getComponentType(): string
    {
        return 'selectFile';
    }

    // @todo move to parent class, and trow exception that it is overridden in the parent class
    public function getSelected(): \Confetti\Components\Map|\model\view\footers\footer_small
    {
        // Get saved value
        $file = $this->contentStore->findOneData($this->relativeContentId);
        // Get default value
        if ($file === null) {
            $component = $this->getComponent();
            $file      = $component->getDecoration('default');
        }

        return match ($file) {
            '/view/footers/small_footer.blade.php' => new \model\view\footers\footer_small(...$this->getParamsForSelectedModel()),
            default                                => throw new \RuntimeException(sprintf('Unknown extended model: %s', $value)),
        };
    }

    // @todo move to parent class, and trow exception that it is overridden in the parent class
    /**
     * @return \model\view\footers\footer_small[]
     */
    public function getOptions(): array
    {
        return [
            '/view/footers/small_footer.blade.php' => new \model\view\footers\footer_small(...$this->getParamsForSelectedModel()),
        ];
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