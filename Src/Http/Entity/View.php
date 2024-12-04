<?php

namespace Src\Http\Entity;

readonly class View
{
    public function __construct(
        private string $view,
        private array  $variables = [],
    )
    {
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }
}