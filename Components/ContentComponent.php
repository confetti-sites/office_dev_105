<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Components;

use Confetti\Helpers\ComponentStandard;
use Random\RandomException;

class ContentComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'content';
    }

    public function __toString(): string
    {
        $value = $this->get();
        if ($value === null) {
            return '';
        }
        if (!is_array($value)) {
            return '<template>Error: Content is not in expected format: '.json_encode($value).'</template>';
        }

    }

    public function get(): ?array
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($content !== null) {
            return json_decode($content, true);
        }

        // Get default value
        if (!$this->contentStore->canFake()) {
            return null;
        }

        return $this->getEditorDataByText($this->generateLoremIpsum());
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
        // The arguments must be hardcoded,
        // do not change the parameter values
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    /**
     * The Label is used as a title for the admin panel
     */
    public function label(string $label): self
    {
        // The arguments must be hardcoded,
        // do not change the parameter values
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    /**
     * Placeholder is used as a hint for the user
     */
    public function placeholder(string $placeholder): self
    {
        // The arguments must be hardcoded,
        // do not change the parameter values
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
            'blocks' => [
                [
                    'id' => newId(),
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
        $max = $component->getDecoration('max')['max'] ?? $this->randomOf([10, 100, 1000]);
        if ($min > $max) {
            $min = $max;
        }

        $size = rand($min, $max);
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
        return $possibilities[array_rand($possibilities)];
    }
}



