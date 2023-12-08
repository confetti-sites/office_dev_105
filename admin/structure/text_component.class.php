<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

return new class extends ComponentStandard {
    public function get(): string
    {
        // Get saved value
        $content = $this->contentStore->find($this->id);
        if ($content !== null) {
            return $content->value;
        }

        // Get default value
        $component = $this->componentStore->find($this->key);
        if ($component->hasDecoration('default')) {
            return $component->getDecoration('default')['value'];
        }

        // Guess value
        $label = $component->getDecoration('label')['value'] ?? '';
        $haystack = strtolower($component->key . $label);
        if (str_contains($haystack, 'address')) {
            return '123 Main St, Anytown, USA 12345';
        }
        if (str_contains($haystack, 'first') && str_contains($haystack, 'name')) {
            return "Sébastien";
        }
        if (str_contains($haystack, 'last') && str_contains($haystack, 'name')) {
            return 'Müller';
        }
        if (str_contains($haystack, 'company') || str_contains($haystack, 'business')) {
            return 'ABC Corporation';
        }
        if (str_contains($haystack, 'mail')) {
            return 'sebastien@example.com';
        }
        if (str_contains($haystack, 'phone')) {
            return '+1 555 123 4567';
        }
        if (str_contains($haystack, 'city')) {
            return 'Anytown';
        }
        if (str_contains($haystack, 'name')) {
            return 'Sébastien Müller';
        }

        // Generate Lorem Ipsum
        // Use different lengths for max to make it more interesting
        $min     = $component->getDecoration('min')['value'] ?? 6;
        $max     = $component->getDecoration('max')['value'] ?? $this->randomOf([10, 100, 1000, 10000]);
        if ($min > $max) {
            $min = $max;
        }

        return $this->generateLoremIpsum(random_int($min, $max));
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
};
