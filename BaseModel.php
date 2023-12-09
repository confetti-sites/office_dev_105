<?php

declare(strict_types=1);

namespace Confetti\Models\BaseModel;

use Confetti\Helpers\ComponentStore;
use Confetti\Helpers\ContentStore;

class BaseModel
{
    public function __construct(
        protected string         $contentId,
        protected ComponentStore $componentStore,
        protected ContentStore   $contentStore,
    )
    {
    }
}
