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

    public function get(): string
    {
        // Get saved value
        $value = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($value !== null) {
            return $value;
        }

        // Get default value
        $component = $this->getComponent();
        if ($component->hasDecoration('default')) {
            return $component->getDecoration('default');
        }

        // Generate random color
        return sprintf('#%06X', random_int(0, 0xFFFFFF));
    }

    public function getViewAdminInput(): string
    {
        return 'structure.color.input';
    }

    public function getViewAdminListItem(): string
    {
        return 'structure.color.list_item';
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



