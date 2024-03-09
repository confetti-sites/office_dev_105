<?php

declare(strict_types=1);

namespace Confetti\Contracts;

use Confetti\Components\Map;

interface SelectModelInterface
{
    public function getSelected(): Map;
}