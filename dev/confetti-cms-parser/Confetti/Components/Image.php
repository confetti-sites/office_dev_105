<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

class Image extends ComponentStandard {
    public function get(): string
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($content !== null) {
            return $content->value;
        }

        $component = $this->getComponent();

        $width = $component->getDecoration('width') ?? 300;
        $height = $component->getDecoration('height') ?? 200;

        return "https://picsum.photos/$width/$height?random=" . rand(0, 10000);
    }

    public function getComponentType(): string
    {
        return 'image';
    }

    // Label is used as a field title in the admin panel
    public function label(string $label): self
    {
        $this->setDecoration('label', [
            'label' => $label,
        ]);
        return $this;
    }

    // Height of the image
    public function height(int $height): self
    {
        $this->setDecoration('height', [
            'height' => $height,
        ]);
        return $this;
    }

    // Width of the image
    public function width(int $width): self
    {
        $this->setDecoration('width', [
            'width' => $width,
        ]);
        return $this;
    }
}