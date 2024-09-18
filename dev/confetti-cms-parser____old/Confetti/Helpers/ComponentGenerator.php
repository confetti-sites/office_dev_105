<?php

declare(strict_types=1);

namespace Confetti\Helpers;

class ComponentGenerator
{
    private string $error = '';

    /**
     * @param Decoration[] $decorations
     */
    public function __construct(
        public readonly string       $name,
        public readonly array        $decorations,
        public readonly string|false $phpClass,
    )
    {
        if ($this->phpClass === false) {
            $this->error = 'phpClass is false: probably you want to get ' .
                'the content of the file, but the file does not exist';
        }
    }

    public function toJson(): string
    {
        header('Content-Type: application/json');
        try {
            return json_encode([
                'name'        => $this->name,
                'decorations' => $this->decorations,
                'php_class'   => base64_encode($this->phpClass),
                'error'       => $this->error,
            ], JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
