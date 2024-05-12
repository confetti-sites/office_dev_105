<?php

declare(strict_types=1);

namespace Confetti\Helpers;

enum Decoration: string
{
    case MATCH = 'match';
    case BY_MODEL = 'byModel';
    case COLUMNS = 'columns';
    case CROP_AUTOMATICALLY = 'cropAutomatically';
    case DEFAULT = 'default';
    case HELP = 'help';
    case LABEL = 'label';
    case MAX = 'max';
    case MIN = 'min';
    case OPTIONS = 'options';
    case PLACEHOLDER = 'placeholder';
    case RATIO = 'ratio';
    case REQUIRED = 'required';
    case SORTABLE = 'sortable';
    case USE_LABEL_FOR = 'useLabelFor';
    case WIDTH_PX = 'widthPx';

    public function comment(string $comment): string
    {
        return $this->value . '//' . $comment;
    }
}
