<?php

namespace Confetti\Helpers;

use Confetti\Components\Map;

class BasicQuery
{
    public function where(string $field, string $operator, mixed $value): self
    {
        return $this;
    }

    /**
     * @return Map[]
     */
    public function get(): array
    {
        return;
    }

    public function first(): Map
    {
        return;
    }
}