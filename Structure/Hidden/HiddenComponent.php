<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Structure\Hidden;

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
        return $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
    }

    public function getViewAdminInput(): string
    {
        return 'admin.structure.hidden.input';
    }

    public static function getViewAdminListItemMjs(): string
    {
        return '/admin/structure/hidden/list_item.mjs';
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





