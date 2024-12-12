<?php

namespace PHPNomad\Fastroute\Component;

use PHPNomad\Di\Interfaces\InstanceProvider;
use PHPNomad\Fastroute\Component\Interfaces\WebController;
use PHPNomad\Rest\Interfaces\Response;
use PHPNomad\Utils\Helpers\Arr;

class WebRoutesRegistry
{
    protected array $routes;

    public function __construct(protected Response $response, protected InstanceProvider $instanceProvider)
    {

    }

    protected function handleRender(WebController $controller): Response
    {
        return $controller->response(clone $this->response);
    }

    public function set(string $route, string $controller)
    {
        unset($this->loadedRoutes);
        $this->routes[] = ['endpoint' => $route, 'controller' => $controller];

        return $this;
    }

    protected function loadRoutes(): array
    {
        $routes = [];

        foreach($this->routes as $route){
            $routes[] = [
                Arr::get($route, 'endpoint'),
                fn() => $this->handleRender($this->instanceProvider->get(Arr::get($route, 'controller')))
            ];
        }

        return $routes;
    }

    public function getRoutes(): array
    {
        if (!isset($this->loadedRoutes)) {
            $this->loadedRoutes = $this->loadRoutes();
        }

        return $this->loadedRoutes;
    }
}