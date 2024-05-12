<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

abstract class Image extends ComponentStandard {
    public function get(): string
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($content !== null) {
            return $content->value;
        }

        $component = $this->getComponent();

        $width = $component->getDecoration('widthPx') ?? 300;
        // @todo get ratio from decoration and calculate the height
        $height = 100;

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

    // Width of the image
    public function widthPx(int $widthPx): self
    {
        $this->setDecoration('widthPx', [
            'widthPx' => $widthPx,
        ]);
        return $this;
    }
}