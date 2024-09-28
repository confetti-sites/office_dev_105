<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Structure\Hidden;

use Confetti\Helpers\ComponentStandard;

abstract class HiddenComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'hidden';
    }

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

    public function getViewAdminInput(): string
    {
        return 'structure.hidden.input';
    }

    /**
     * The Label is used as a title for the admin panel
     */
    public function label(string $label): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }
}





