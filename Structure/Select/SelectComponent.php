<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Structure\Select;

use Confetti\Helpers\ComponentStandard;

class SelectComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'select';
    }

    public function get(bool $random = false): ?string
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($content !== null || !$random) {
            return $content;
        }

        $component = $this->getComponent();

        // Get random value from all options
        $options = $component->getDecoration('options');
        if (empty($options)) {
            return '';
        }

        $key = array_rand($options, 1);
        return $options[$key]['id'];
    }

    public function getViewAdminInput(): string
    {
        return 'admin.structure.select.input';
    }

    public static function getViewAdminListItemMjs(): string
    {
        return '/admin/structure/select/list_item.mjs';
    }

    /**
     * Without saved value, this will be the default.
     */
    public function default(string $default): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    /**
     * Label is used as a field title in the admin panel.
     */
    public function label(string $label): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    /**
     * List of options. For now, only values are supported.
     */
    public function options(array $options): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    /**
     * The user can't select the `Nothing selected` option.
     */
    public function required(): self
    {
        $this->setDecoration(__FUNCTION__, true);
        return $this;
    }
}



