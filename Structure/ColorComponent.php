<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Structure;

use Confetti\Helpers\ComponentStandard;

class ColorComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'color';
    }

    public function get(bool $random = false): ?string
    {
        // Get saved value
        $value = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($value !== null) {
            return htmlspecialchars($value);
        }

        if ($random) {
            // Generate random color
            return sprintf('#%06X', random_int(0, 0xFFFFFF));
        }

        return null;
    }

    /**
     * The return value is a full path from the root to a blade file.
     */
    public function getViewAdminInput(): string
    {
        return 'admin.structure.color.input';
    }

    /**
     * The return value is a full path from the root to a mjs file.
     */
    public static function getViewAdminListItemMjs(): string
    {
        return '/admin/structure/color/list_item.mjs';
    }

    // Label is used as a title for the admin panel
    public function label(string $label): self
    {
        // The arguments must be hardcoded, any additional
        // values can only be used within this class.
        return $this;
    }

    // Help is used as a description for the admin panel
    public function help(string $help): self
    {
        // The arguments must be hardcoded, any additional
        // values can only be used within this class.
        return $this;
    }

    // Default value is used when the user hasn't saved any value
    public function default(string $default): self
    {
        // The arguments must be hardcoded, any additional
        // values can only be used within this class.
        return $this;
    }
}



