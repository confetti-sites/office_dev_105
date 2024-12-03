<?php

namespace Src\Http\Entity;

class View
{
    private string $view;
    private array $variables;

    public function __construct()
    {
    }

    public function view(string $view, array $data = []): self
    {
        $this->view      = $view;
        $this->variables = $data;

        return $this;
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