<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

class Image extends ComponentStandard {
    public function getComponentType(): string
    {
        return 'image';
    }

    public function get(): string
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->relativeContentId);
        if ($content !== null) {
            return $content->value;
        }

        // Get default value
        $component = $this->getComponent();
        $width = $component->getDecoration('width') ?? 300;
        $height = $component->getDecoration('height') ?? 200;

        return "https://picsum.photos/$width/$height?random=" . rand(0, 10000);
    }

    // Label is used as a field title in the admin panel
    public function label(string $label): self
    {
        return $this;
    }

    // Height of the image
    public function height(int $height): self
    {
        return $this;
    }

    // Width of the image
    public function width(int $width): self
    {
        return $this;
    }
}