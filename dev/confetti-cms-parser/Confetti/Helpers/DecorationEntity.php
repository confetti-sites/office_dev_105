<?php

declare(strict_types=1);

namespace Confetti\Helpers;

class DecorationEntity
{
    public function __construct(
        public readonly string $type,
        public readonly array $data,
    )
    {

    }
}
