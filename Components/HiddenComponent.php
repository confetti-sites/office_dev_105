<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Components;

use Confetti\Helpers\ComponentStandard;

class HiddenComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'hidden';
    }

    public function get(): ?string
    {
        if ($this->contentStore === null) {
            throw new \RuntimeException('This component is only used as a reference. Therefore, you can\'t call __toString() or get().');
        }
        // Get saved value
        $content = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($content == null) {
            return null;
        }
        return (string) $content;
    }

    public function getViewAdminInput(): string
    {
        return 'admin.components.hidden.input';
    }

    public static function getViewAdminPreview(): string
    {
        return '/admin/components/hidden/preview.mjs';
    }

    /**
     * The Label is used as a title for the admin panel
     */
    public function label(string $label): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    /**
     * The default value will be used if no value is saved
     */
    public function default(string $default): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }
}





