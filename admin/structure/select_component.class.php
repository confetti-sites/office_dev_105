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
        $component = $this->componentStore->findOrNull($this->key);
        if ($component !== null) {
            return $this->getValueFromOptions($component);
        }
        $component = $this->componentStore->findOrNull($this->key . '/-');
        if ($component !== null) {
            return $this->getValueFromFileInDirectories($component);
        }
        return '!!! Error: Component with type `select` need to have decoration `options` or `fileInDirectories` !!!';
    }

    public function getValueFromOptions(ComponentEntity $component): string
    {
        // Get saved value
        $content = $this->contentStore->find($this->id);
        if ($content !== null) {
            return $content->value;
        }

        // Get default value
        if ($component->hasDecoration('default')) {
            return $component->getDecoration('default')['value'];
        }

        // Get random value from all options
        $options = $component->getDecoration('options')['options'];
        if (count($options) === 0) {
            return '';
        }
        // random index from 0 to count($options) - 1
        $index = rand(0, count($options) - 1);
        return $options[$index]['id'];
    }

    public function getValueFromFileInDirectories(ComponentEntity $component): string
    {
        // Get saved value
        $filePath = $this->contentStore->find($this->id)?->value;
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

    public static function getDefaultOption(ComponentEntity $component): string
    {
        return $component->getDecoration('default')['value'] ?? '';
    }

    public static function getAllOptions(ComponentStore $store, ComponentEntity $component): array
    {
        $options = [];
        if ($component->hasDecoration('fileInDirectories')) {
            $targets  = $component->getDecoration('fileInDirectories')['targets'];
            foreach ($targets as $target) {
                foreach ($store->whereMatch($target) as $file) {
                    $options[$file->key] = self::fileNameToLabel($file->source->file);
                }
            }
        }
        if ($component->hasDecoration('options')) {
            foreach ($component->getDecoration('options')['options'] as $option) {
                $options[$option['id']] = $option['label'];
            }
        }
        return $options;
    }

    public function toMap(): Map
    {
        return new Map(
            $this->id . '/-',
            ComponentStore::newWherePrefix($this->id . '/-'),
            new ContentStore(),
        );
    }

    private static function getViewByPath(string $path): string
    {
        $path = str_replace('.blade.php', '', $path);
        return str_replace('/', '.', $path);
    }

    private static function fileNameToLabel(string $file): string
    {
        $name = basename($file, '.blade.php');
        $name = str_replace(['-', '_'], ' ', $name);
        return ucwords($name);
    }
};
