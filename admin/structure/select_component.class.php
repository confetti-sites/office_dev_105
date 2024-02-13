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
        $component = $this->componentStore->findOrNull($this->relativeContentId);
        if ($component !== null) {
            return $this->getValueFromOptions($component);
        }
        return '!!! Error: Component with type `select` need to have decoration `options` !!!';
    }

    public function getValueFromOptions(ComponentEntity $component): string
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->relativeContentId);
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

    public static function getAllOptions(ComponentStore $store, ComponentEntity $component): array
    {
        $options = [];
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
            $this->relativeContentId,
            ComponentStore::newWherePrefix($this->relativeContentId),
            new ContentStore(),
        );
    }
};
