<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentEntity;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;
use Confetti\Helpers\HasMapInterface;
use RuntimeException;

return new class extends ComponentStandard implements \Confetti\Contracts\SelectModelInterface {
    public function get(): string
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($content !== null) {
            return $content->value;
        }

        $component = $this->getComponent();

        // Get default value
        $default = $component->getDecoration('default');
        if ($default) {
            return (string) $default;
        }

        // Get random value from all options
        $options = $component->getDecoration('options');
        if (count($options) === 0) {
            return '';
        }

        // random index from 0 to count($options) - 1
        $index = rand(0, count($options) - 1);
        return $options[$index];
    }

    public static function getAllOptions(ComponentEntity $component): array
    {
        $options = [];
        foreach ($component->getDecoration('options') ?? [] as $option) {
            $options[$option['id']] = $option['label'];
        }
        return $options;
    }
};
