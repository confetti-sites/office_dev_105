<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

return new class extends ComponentStandard {
    public function get(): string
    {
        if ($this->contentStore === null) {
            throw new \RuntimeException('This component is only used as a reference. Therefore, you can\'t call __toString() or get().');
        }
        // Get saved value
        $content = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($content !== null) {
            return $content;
        }

        // Get default value
        $component = $this->getComponent();
        if ($component->hasDecoration('default')) {
            return $component->getDecoration('default')['value'];
        }

        return '';
    }

    public function __toString(): string
    {
        return $this->get();
    }
};
