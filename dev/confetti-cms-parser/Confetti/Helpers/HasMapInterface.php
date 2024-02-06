<?php

declare(strict_types=1);

namespace Confetti\Helpers;


use Confetti\Components\Map;

interface HasMapInterface
{
    public function toMap(): Map;
}
