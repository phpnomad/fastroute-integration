<?php

namespace PHPNomad\FastRoute\Component\Registries;

use PHPNomad\Di\Interfaces\InstanceProvider;
use PHPNomad\Http\Interfaces\Response;
use PHPNomad\Rest\Interfaces\WebController;
use PHPNomad\Utils\Helpers\Arr;

class WebRoutesRegistry
{
    protected array $routes = [];
    protected ?array $loadedRoutes = null;

    public function __construct(protected Response $response, protected InstanceProvider $instanceProvider)
    {

    }

    protected function handleRender(WebController $controller): Response
    {
        return $controller->response(clone $this->response);
    }

    public function set(string $route, string $controller)
    {
        $this->loadedRoutes = null;
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
        if (is_null($this->loadedRoutes)) {
            $this->loadedRoutes = $this->loadRoutes();
        }

        return $this->loadedRoutes;
    }
}
