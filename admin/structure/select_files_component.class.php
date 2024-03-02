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
        $component = $this->getComponent();

        // Get saved value
        $filePath = $this->contentStore->findOneData($this->getFullContentId())?->value;
        if ($filePath !== null) {
            if (str_ends_with($filePath, '.blade.php')) {
                return self::getViewByPath($filePath);
            }
            return $filePath;
        }

        // Get default view
        $filePath = $component->getDecoration('default') ?? throw new RuntimeException('Error: No default defined. Use ->default(\'filename_without_directory\') to define the default value. In ' . $component->source);
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
