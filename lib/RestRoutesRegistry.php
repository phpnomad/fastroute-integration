<?php

namespace PHPNomad\Component\RestIntegration;

use PHPNomad\Utils\Helpers\Arr;

class RestRoutesRegistry
{
    protected array $setters;

    protected array $routes;

    public function getSetters(): array
    {
        if (!isset($this->routes)) {
            $this->routes = Arr::map($this->setters, fn(callable $routeGetter) => $routeGetter());
        }

        return $this->routes;
    }

    public function set(callable $setter)
    {
        $this->setters[] = $setter;
    }
}