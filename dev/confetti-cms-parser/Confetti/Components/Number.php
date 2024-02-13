<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

class Number extends ComponentStandard {
    public function get(): string
    {
        return (string)$this->toInt();
    }

    public function toInt(): int
    {
        // Get saved value
        $content = $this->contentStore->find($this->relativeContentId);
        if ($content !== null) {
            return (int)$content->value;
        }

        // Use default value
        $component = $this->componentStore->find($this->relativeContentId);
        if ($component->hasDecoration('default')) {
            return (int)$component->getDecoration('default')['value'];
        }

        // Generate random number
        // Use different lengths for min and max to make it more interesting
        $min = $component->getDecoration('min')['value'] ?? $this->randomOf([-10, -100, -1000, -10000]);
        $max = $component->getDecoration('max')['value'] ?? $this->randomOf([10, 100, 1000, 10000]);

        return random_int($min, $max);
    }

    private function randomOf(array $possibilities): int
    {
        return $possibilities[array_rand($possibilities)];
    }

    // Default will be used if no value is saved
    public function default(string $default): self
    {
        return $this;
    }

    // Label is used as a field title in the admin panel
    public function label(string $label): self
    {
        return $this;
    }

    // Minimum number
    public function min(int $min): self
    {
        return $this;
    }

    // Maximum number
    public function max(int $max): self
    {
        return $this;
    }

    // The placeholder text for the input field
    public function placeholder(string $placeholder): self
    {
        return $this;
    }
}