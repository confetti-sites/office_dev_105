<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Components;

use Confetti\Helpers\ComponentStandard;

class ContentComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'content';
    }

    public function get(): ?array
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($content !== null) {
            $result = json_decode((string) $content, true);
            if (is_array($result)) {
                return $result;
            }
            return null;
        }

        // Get default value
        if (!$this->contentStore->canFake()) {
            return null;
        }

        return $this->getEditorDataByText($this->generateLoremIpsum());
    }

    public function __toString(): string
    {
        $value = $this->get();
        if ($value === null) {
            return '';
        }
        if (!is_array($value)) {
            return '<template>Error: Content is not in expected format: ' . json_encode($value) . '</template>';
        }
        return 'Error: Can not render to string. Include all blocks instead. Example: @include(\'website.includes.blocks.index\', [\'model\' => $contentRow->content(\'content\')])';
    }

    public function isEmpty(): bool
    {
        if (empty($this->get())) {
            return true;
        }

        if (empty($this->get()['blocks'])) {
            return true;
        }

        // When no text is set, the editor will save a null value
        if (array_key_exists('text', $this->get()['blocks'][0]['data']) && $this->get()['blocks'][0]['data']['text'] === null) {
            return true;
        }

        return false;
    }

    public function getViewAdminInput(): string
    {
        return 'admin.components.content.input';
    }

    public static function getViewAdminPreview(): string
    {
        return '/admin/components/content/preview.mjs';
    }

    /**
     * Default value is used when the user hasn't saved any value
     */
    public function default(string $default): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
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
     * Placeholder is used as a hint for the user
     */
    public function placeholder(string $placeholder): self
    {
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    public function getDefaultData(): array
    {
        return $this->getEditorDataByText($this->getComponent()->getDecoration('default'));
    }

    private function getEditorDataByText(mixed $value): array
    {
        return [
            'blocks'  => [
                [
                    'id'   => newId(),
                    'type' => 'paragraph',
                    'data' => [
                        'text' => $value,
                    ],
                ],
            ],
            'version' => '2.29.1',
        ];
    }

    private function generateLoremIpsum(): string
    {
        $component = $this->getComponent();

        // Generate Lorem Ipsum
        // Use different lengths for max to make it more interesting
        $min = $component->getDecoration('min')['min'] ?? 6;
        $max = $component->getDecoration('max')['max'] ?? $this->randomOf([10, 200, 3000]);
        if ($min > $max) {
            $min = $max;
        }

        $size  = rand($min, $max);
        $words = ['lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit', 'praesent', 'interdum', 'dictum', 'mi', 'non', 'egestas', 'nulla', 'in', 'lacus', 'sed', 'sapien', 'placerat', 'malesuada', 'at', 'erat', 'etiam', 'id', 'velit', 'finibus', 'viverra', 'maecenas', 'mattis', 'volutpat', 'justo', 'vitae', 'vestibulum', 'metus', 'lobortis', 'mauris', 'luctus', 'leo', 'feugiat', 'nibh', 'tincidunt', 'a', 'integer', 'facilisis', 'lacinia', 'ligula', 'ac', 'suspendisse', 'eleifend', 'nunc', 'nec', 'pulvinar', 'quisque', 'ut', 'semper', 'auctor', 'tortor', 'mollis', 'est', 'tempor', 'scelerisque', 'venenatis', 'quis', 'ultrices', 'tellus', 'nisi', 'phasellus', 'aliquam', 'molestie', 'purus', 'convallis', 'cursus', 'ex', 'massa', 'fusce', 'felis', 'fringilla', 'faucibus', 'varius', 'ante', 'primis', 'orci', 'et', 'posuere', 'cubilia', 'curae', 'proin', 'ultricies', 'hendrerit', 'ornare', 'augue', 'pharetra', 'dapibus'];
        $lorem = '';
        while ($size > 0) {
            $randomWord = array_rand($words);
            $lorem      .= $words[$randomWord] . ' ';
            $size       -= strlen($words[$randomWord]);
        }
        return ucfirst($lorem);
    }

    private function randomOf(array $possibilities): int
    {
        // Use a static variable to keep the state between calls
        // That way, we always get different possibilities
        static $i = null;

        // We want to begin with a random value
        if ($i === null) {
            $i = array_rand($possibilities);
        }
        $i++;

        // If we reach the end of the array, start over
        if ($i >= count($possibilities)) {
            $i = 0;
        }
        return $possibilities[$i];
    }
}
