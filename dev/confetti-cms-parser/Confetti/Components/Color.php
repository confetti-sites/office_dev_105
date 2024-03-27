<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

abstract class Color extends ComponentStandard {
    public function get(): string
    {
        // Get saved value
        $value = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($value !== null) {
            return $value->value;
        }

        // Get default value
        $component = $this->componentStore->find($this->getId());
        if ($component->hasDecoration('default')) {
            return $component->getDecoration('default')['value'];
        }

        // Generate random color
        return sprintf('#%06X', random_int(0, 0xFFFFFF));
    }

    // If no value is given, this will be used
    public function default(string $default): self
    {
        return $this;
    }

    // Label is used as a field title in the admin panel
    public function label(string $label): self
    {
        return $this;
    }
}