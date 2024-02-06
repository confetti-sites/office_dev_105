<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentEntity;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;
use Confetti\Helpers\HasMapInterface;
use RuntimeException;

class SelectFiles extends ComponentStandard implements HasMapInterface {
    public function get(): string
    {
        $component = $this->componentStore->findOrNull($this->componentKey . '-');
        if ($component !== null) {
            return $this->getValueFromInDirectories($component);
        }
        return '!!! Error: Component with type `select` need to have decoration `options` or `inDirectories` !!!';
    }

    public function getValueFromInDirectories(ComponentEntity $component): string
    {
        // Get saved value
        $filePath = $this->contentStore->find($this->contentId)?->value;
        if ($filePath !== null) {
            if (str_ends_with($filePath, '.blade.php')) {
                return self::getViewByPath($filePath);
            }
            return $filePath;
        }

        // Get default view
        $filePath = $component->getDecoration('default')['value'] ?? throw new RuntimeException('Error: No default defined. Use ->default(\'filename_without_directory\') to define the default value. In ' . $component->source);
        if (str_ends_with($filePath, '.blade.php')) {
            return self::getViewByPath($filePath);
        }
        return $filePath;
    }

    public function toMap(): Map
    {
        return new Map(
            $this->contentId . '-',
            ComponentStore::newWherePrefix($this->contentId . '-'),
            new ContentStore(),
        );
    }

    private static function getViewByPath(string $path): string
    {
        $path = str_replace('.blade.php', '', $path);
        return str_replace('/', '.', $path);
    }

    // List all files by directories. You can use the glob pattern. For example: `->inDirectories(['/view/footers'])`
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
    public function inDirectories(array $inDirectories): self
    {
        return $this;
    }

    // Before saving this will be the default file. With inDirectories, the file must be in the directory.
    public function default(string $default): self
    {
        return $this;
    }

    // Label is used as a field title in the admin panel
    public function label(string $label): self
    {
        return $this;
    }
}