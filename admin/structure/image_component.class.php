<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

return new class extends ComponentStandard {
    public function get(): string
    {
        // Get saved value
        $content = $this->contentStore->find($this->id);
        if ($content !== null) {
            return $content->value;
        }

        // Get default value
        $component = $this->componentStore->findOrNull($this->key);
        $width = $component?->getDecoration('width')['value'] ?? 300;
        $height = $component?->getDecoration('height')['value'] ?? 200;

        return "https://picsum.photos/$width/$height?random=" . rand(0, 10000);
    }
};
