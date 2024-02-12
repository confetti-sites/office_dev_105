<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentEntity;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;
use Confetti\Helpers\HasMapInterface;
use RuntimeException;

return new class extends ComponentStandard implements HasMapInterface {
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
        $filePath = $this->contentStore->find($this->getFullContentId())?->value;
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
            $this->relativeContentId . '-',
            ComponentStore::newWherePrefix($this->relativeContentId . '-'),
            new ContentStore(),
        );
    }

    private static function getViewByPath(string $path): string
    {
        $path = str_replace('.blade.php', '', $path);
        return str_replace('/', '.', $path);
    }
};
