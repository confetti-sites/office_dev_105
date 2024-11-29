<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Components;

use Confetti\Helpers\ComponentStandard;

class ColorComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'color';
    }

    public function get(): ?string
    {
        // Get saved value
        $value = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($value !== null) {
            return htmlspecialchars((string)$value);
        }
        if (!$this->contentStore->canFake()) {
            return null;
        }

        // Generate random color
        return sprintf('#%06X', rand(0, 0xFFFFFF));
    }

    /**
     * The return value is a full path from the root to a blade file.
     */
    public function getViewAdminInput(): string
    {
        return 'admin.components.color.input';
    }

    /**
     * The return value is a full path from the root to a mjs file.
     */
    public static function getViewAdminPreview(): string
    {
        return '/admin/components/color/preview.mjs';
    }

    // Label is used as a title for the admin panel
    public function label(string $label): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    // Help is used as a description for the admin panel
    public function help(string $help): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    // Default value is used when the user hasn't saved any value
    public function default(string $default): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }
}



