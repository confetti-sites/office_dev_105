<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

return new class extends ComponentStandard {
    public function get(): string
    {
        // Get saved value
        $value = $this->contentStore->find($this->id);
        if ($value !== null) {
            return $value->value;
        }

        // Get default value
        $component = $this->componentStore->find($this->id);
        if ($component->hasDecoration('default')) {
            return $component->getDecoration('default')['value'];
        }

        // Generate random color
        return sprintf('#%06X', random_int(0, 0xFFFFFF));
    }
};
