<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Components;

use Confetti\Helpers\ComponentStandard;

class DiscussionComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'discussion';
    }

    public function get(): ?array
    {
        // Get saved value
        $value = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($value !== null) {
            try {
                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new \RuntimeException('Invalid JSON in content. JSON: ' . $content);
            }
        }
        if (!$this->contentStore->canFake()) {
            return null;
        }

        // Generate random GitHub discussion with HTML body
        return [
            'url' => 'https://github.com/confetti-cms/community/discussions/1',
            'discussion' => [
                'body' => '<p>Discussion body</p>',
            ],
        ];
    }

    public function getHtml(): string
    {
        $value = $this->get();
        if (empty($value['discussion']) || empty($value['discussion']['body'])) {
            return 'Content missing';
        }
        return $value['discussion']['body'];
    }

    /**
     * The return value is a full path from the root to a blade file.
     */
    public function getViewAdminInput(): string
    {
        return 'admin.components.discussion.input';
    }

    /**
     * The return value is a full path from the root to a mjs file.
     */
    public static function getViewAdminPreview(): string
    {
        return '/admin/components/discussion/preview.mjs';
    }

    // Help is used as a description for the admin panel
    public function help(string $help): self
    {
        // The arguments must be hardcoded,
        // do not change the parameter values
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    // Label is used as a title for the admin panel
    public function label(string $label): self
    {
        // The arguments must be hardcoded,
        // do not change the parameter values
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }
}



