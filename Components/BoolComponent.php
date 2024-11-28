<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Components;

use Confetti\Helpers\ComponentStandard;

class BoolComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'bool';
    }

    public function get(): ?bool
    {
        // Get saved value
        $value = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($value !== null) {
            // Value can be other types than bool, like a string from a previous/other type. So cast it strictly to bool.
            return $value === true;
        }

        if ($this->contentStore->canFake()) {
            return $this->random();
        }

        return null;
    }

    /**
     * The return value is a full path from the root to a blade file.
     */
    public function getViewAdminInput(): string
    {
        return 'admin.components.bool.input';
    }

    /**
     * The return value is a full path from the root to a mjs file.
     */
    public static function getViewAdminPreview(): string
    {
        return '/admin/components/bool/preview.mjs';
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
    public function default(bool $default): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    private function random(): mixed
    {
        return [true, false][array_rand([true, false])];
    }
}



