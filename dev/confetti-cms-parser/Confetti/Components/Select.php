<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentEntity;
use Confetti\Helpers\ComponentStandard;
use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;

class Select extends ComponentStandard {
    public function get(): string
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->relativeContentId);
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

        $key = array_rand($options, 1);
        return $options[$key]['id'];
    }

    public function getOptions(): array
    {
        $options = [];
        $decorations = $this->getComponent()->getDecoration('options') ?? [];
        foreach ($decorations as $option) {
            $options[$option['id']] = $option['label'];
        }
        return $options;
    }

    public function getComponentType(): string
    {
        return 'select';
    }

    // Before saving this will be the default.
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

    // List of options. For now, only values are supported.
    public function options(array $options): self
    {
        $this->setDecoration('options', [
            'options' => $options,
        ]);
        return $this;
    }
}