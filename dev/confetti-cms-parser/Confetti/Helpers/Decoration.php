<?php

declare(strict_types=1);

namespace Confetti\Helpers;

enum Decoration: string
{
    case BY_DIRECTORY = 'inDirectories';
    case BY_MODEL = 'byModel';
    case COLUMNS = 'columns';
    case CROP_AUTOMATICALLY = 'cropAutomatically';
    case DEFAULT = 'default';
    case HEIGHT = 'height';
    case HEIGHT_MAX = 'heightMax';
    case HEIGHT_MIN = 'heightMin';
    case HELP = 'help';
    case LABEL = 'label';
    case MAX = 'max';
    case MIN = 'min';
    case OPTIONS = 'options';
    case PLACEHOLDER = 'placeholder';
    case RATIO = 'ratio';
    case REQUIRED = 'required';
    case WIDTH = 'width';
    case WIDTH_MAX = 'widthMax';
    case WIDTH_MIN = 'widthMin';

    public function comment(string $comment): string
    {
        return $this->value . '//' . $comment;
    }
}
