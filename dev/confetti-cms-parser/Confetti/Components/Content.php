<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

abstract class Content extends ComponentStandard {
    public function get(): array
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($content !== null) {
            return $content;
        }

        // Get default value
        $component = $this->getComponent();
        $default = $component->getDecoration('default');
        if ($default) {
            return $this->getEditorDataByText($default);
        }

        if (!$this->contentStore->canFake()) {
            return [];
        }

        // Generate Lorem Ipsum
        // Use different lengths for max to make it more interesting
        $min     = $component->getDecoration('min') ?? 6;
        $max     = $component->getDecoration('max') ?? $this->randomOf([10, 100, 1000]);
        if ($min > $max) {
            $min = $max;
        }

        return $this->getEditorDataByText($this->generateLoremIpsum(random_int($min, $max)));
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
            'version' => '2.29.1'
        ];
    }

    private function generateLoremIpsum(int $size): string
    {
        $words = ['lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit', 'praesent', 'interdum', 'dictum', 'mi', 'non', 'egestas', 'nulla', 'in', 'lacus', 'sed', 'sapien', 'placerat', 'malesuada', 'at', 'erat', 'etiam', 'id', 'velit', 'finibus', 'viverra', 'maecenas', 'mattis', 'volutpat', 'justo', 'vitae', 'vestibulum', 'metus', 'lobortis', 'mauris', 'luctus', 'leo', 'feugiat', 'nibh', 'tincidunt', 'a', 'integer', 'facilisis', 'lacinia', 'ligula', 'ac', 'suspendisse', 'eleifend', 'nunc', 'nec', 'pulvinar', 'quisque', 'ut', 'semper', 'auctor', 'tortor', 'mollis', 'est', 'tempor', 'scelerisque', 'venenatis', 'quis', 'ultrices', 'tellus', 'nisi', 'phasellus', 'aliquam', 'molestie', 'purus', 'convallis', 'cursus', 'ex', 'massa', 'fusce', 'felis', 'fringilla', 'faucibus', 'varius', 'ante', 'primis', 'orci', 'et', 'posuere', 'cubilia', 'curae', 'proin', 'ultricies', 'hendrerit', 'ornare', 'augue', 'pharetra', 'dapibus', 'nullam', 'sollicitudin', 'euismod', 'eget', 'pretium', 'vulputate', 'urna', 'arcu', 'porttitor', 'quam', 'condimentum', 'consequat', 'tempus', 'hac', 'habitasse', 'platea', 'dictumst', 'sagittis', 'gravida', 'eu', 'commodo', 'dui', 'lectus', 'vivamus', 'libero', 'vel', 'maximus', 'pellentesque', 'efficitur', 'class', 'aptent', 'taciti', 'sociosqu', 'ad', 'litora', 'torquent', 'per', 'conubia', 'nostra', 'inceptos', 'himenaeos', 'fermentum', 'turpis', 'donec', 'magna', 'porta', 'enim', 'curabitur', 'odio', 'rhoncus', 'blandit', 'potenti', 'sodales', 'accumsan', 'congue', 'neque', 'duis', 'bibendum', 'laoreet', 'elementum', 'suscipit', 'diam', 'vehicula', 'eros', 'nam', 'imperdiet', 'sem', 'ullamcorper', 'dignissim', 'risus', 'aliquet', 'habitant', 'morbi', 'tristique', 'senectus', 'netus', 'fames', 'nisl', 'iaculis', 'cras', 'aenean'];
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

    public function getComponentType(): string
    {
        return 'text';
    }

    // Default will be used if no value is saved
    public function default(string $default): self
    {
        $this->setDecoration('default', [
            'default' => $default,
        ]);
        return $this;
    }

    // Label is used as a field title in the admin panel
    public function label(string $label): self
    {
        $this->setDecoration('label', [
            'label' => $label,
        ]);
        return $this;
    }

    // Minimum number of characters
    public function min(int $min): self
    {
        $this->setDecoration('min', [
            'min' => $min,
        ]);
        return $this;
    }

    // Maximum number of characters
    public function max(int $max): self
    {
        $this->setDecoration('max', [
            'max' => $max,
        ]);
        return $this;
    }

    // The placeholder text for the input field
    public function placeholder(string $placeholder): self
    {
        $this->setDecoration('placeholder', [
            'placeholder' => $placeholder,
        ]);
        return $this;
    }
}