<?php

namespace Vangel\Project\Core;

class View
{

    private mixed $model = null;

    public function __construct(private readonly string $view)
    {
    }

    public function render(): string
    {
        ob_start();

        $model = $this->model;

        include VIEW_PATH . "/{$this->view}.php";

        $viewData = ob_get_clean();

        return (string)$viewData;
    }

    public static function of(string $view): static
    {
        return new static($view);
    }

    public function with(mixed $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
