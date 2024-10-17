<?php /** @noinspection DuplicatedCode */

declare(strict_types=1);

namespace Src\Structure;

use Confetti\Helpers\ComponentStandard;

class TextComponent extends ComponentStandard
{
    public function type(): string
    {
        return 'text';
    }

    public function get(): ?string
    {
        // Get saved value
        $content = $this->contentStore->findOneData($this->parentContentId, $this->relativeContentId);
        if ($content !== null) {
            return $content;
        }

        // Get default value
        if (!$this->contentStore->canFake()) {
            return null;
        }

        // Guess value
        $component = $this->getComponent();
        $label = $component->getDecoration('label.value') ?? '';
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
        if (str_contains($haystack, 'name')) {
            return 'Sébastien Müller';
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

        // Generate Lorem Ipsum
        // Use different lengths for max to make it more interesting
        $min     = $component->getDecoration('min', 'min') ?? 6;
        $max     = $component->getDecoration('max', 'max') ?? $this->randomOf([10, 100, 1000]);
        if ($min > $max) {
            $min = $max;
        }

        return $this->generateLoremIpsum(rand($min, $max));
    }

    public function getViewAdminInput(): string
    {
        return 'admin.structure.text.input';
    }

    public static function getViewAdminPreview(): string
    {
        return '/admin/structure/text/preview.mjs';
    }

    // Default will be used if no value is saved
    public function default(string $default): self
    {
        // The arguments must be hardcoded,
        // do not change the parameter values
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    // Label is used as a field title in the admin panel
    public function label(string $label): self
    {
        // The arguments must be hardcoded,
        // do not change the parameter values
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    // Minimum number of characters
    public function min(int $min): self
    {
        // The arguments must be hardcoded,
        // do not change the parameter values
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    // Maximum number of characters
    public function max(int $max): self
    {
        // The arguments must be hardcoded,
        // do not change the parameter values
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    // The placeholder text for the input field
    public function placeholder(string $placeholder): self
    {
        // The arguments must be hardcoded,
        // do not change the parameter values
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    // Help text is shown below the input field
    public function help(string $help): self
    {
        // The arguments must be hardcoded,
        // do not change the parameter values
        $this->setDecoration(__FUNCTION__, get_defined_vars());
        return $this;
    }

    private function generateLoremIpsum(int $size): string
    {
        $words = ['lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit', 'praesent', 'interdum', 'dictum', 'mi', 'non', 'egestas', 'nulla', 'in'];
        $lorem = '';
        while ($size > 0) {
            $randomWord = array_rand($words);
            $lorem      .= $words[$randomWord] . ' ';
            $size       -= strlen($words[$randomWord]);
        }
        return trim(ucfirst($lorem));
    }

    private function randomOf(array $possibilities): int
    {
        return $possibilities[array_rand($possibilities)];
    }
}



