<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

return new class extends ComponentStandard {
    public function get(): string
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->relativeContentId);
        if ($content !== null) {
            return $content->value;
        }

        $component = $this->getComponent();
        $width = $component->getDecoration('width') ?? 300;
        $height = $component->getDecoration('height') ?? 200;

        return "https://picsum.photos/$width/$height?random=" . rand(0, 10000);
    }
};
