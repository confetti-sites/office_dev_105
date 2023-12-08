<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

return new class extends ComponentStandard {
    public function get(): string
    {
        return (string)$this->toInt();
    }

    public function toInt(): int
    {
        // Get saved value
        $content = $this->contentStore->find($this->id);
        if ($content !== null) {
            return (int)$content->value;
        }

        // Use default value
        $component = $this->componentStore->find($this->id);
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
};
