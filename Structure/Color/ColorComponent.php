<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Structure\Color;

use Confetti\Components\FilePatternArray;
use Confetti\Helpers\ComponentStandard;

abstract class ColorComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'color';
    }

    public function get(bool $random = false): string
    {
        // Get saved value
        $value = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($value !== null) {
            return htmlspecialchars($value);
        }

        // Get default value
        $component = $this->getComponent();
        if ($component->hasDecoration('default')) {
            return htmlspecialchars($component->getDecoration('default'));
        }

        if ($random) {
            // Generate random color
            return sprintf('#%06X', random_int(0, 0xFFFFFF));
        }

        return '';
    }

    public function getViewAdminInput(): string
    {
        return 'admin.structure.color.input';
    }

    public static function getViewAdminListItemMjs(): string
    {
        return '/admin/structure/color/list_item.mjs';
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



