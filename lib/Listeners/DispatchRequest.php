<?php

namespace PHPNomad\FastRoute\Component\Listeners;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use PHPNomad\Auth\Enums\SessionContexts;
use PHPNomad\Auth\Interfaces\CurrentContextResolverStrategy;
use PHPNomad\Events\Interfaces\CanHandle;
use PHPNomad\Events\Interfaces\Event;
use PHPNomad\FastRoute\Component\Events\RequestInitiated;
use PHPNomad\FastRoute\Component\Registries\RestRoutesRegistry;
use PHPNomad\FastRoute\Component\Registries\WebRoutesRegistry;
use PHPNomad\Rest\Enums\Method;
use PHPNomad\Rest\Exceptions\RestException;
use PHPNomad\Rest\Interfaces\Request;
use PHPNomad\Rest\Interfaces\Response;

use function FastRoute\simpleDispatcher;

/**
 * @extends CanHandle<RequestInitiated>
 */
class DispatchRequest implements CanHandle
{
    public function __construct(
      protected CurrentContextResolverStrategy $context,
      protected RestRoutesRegistry             $restRouteRegistry,
      protected WebRoutesRegistry              $webRouteRegistry,
      protected Request                        $request,
      protected Response                       $response
    )
    {

    }

    protected function getEndpointsForContext(RouteCollector $r)
    {
        if($this->context->getCurrentContext() === SessionContexts::Rest) {
            foreach ($this->restRouteRegistry->getSetters() as $route) {
                [$method, $endpoint, $handler] = $route;
                $r->addRoute($method, $endpoint, $handler);
            }
        }

        if($this->context->getCurrentContext() === SessionContexts::Web){
            foreach ($this->webRouteRegistry->getRoutes() as $route) {
                [$endpoint, $handler] = $route;
                $r->addRoute(Method::Get, $endpoint, $handler);
            }
        }
    }


    public function handle(Event $event): void
    {
        $routeInfo = simpleDispatcher(fn(RouteCollector $r) => $this->getEndpointsForContext($r))
          ->dispatch($event->method, $event->uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $response = clone $this->response;
                $response->setJson(['Route not found']);
                $event->setResponse($response);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new RestException('Method not allowed');
            case Dispatcher::FOUND:

                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                // Create the Request object with the route parameters
                $request = clone $this->request;

                // Set route-specific parameters from $vars
                foreach ($vars as $key => $value) {
                    $request->setParam($key, $value);
                }

                // Set global request parameters (GET, POST, etc.)
                foreach ($_REQUEST as $key => $value) {
                    $request->setParam($key, $value);
                }

                // Set headers from global server variables
                foreach ($this->getAllHeaders() as $name => $value) {
                    $request->setHeader($name, $value);
                }

                $event->setResponse($handler($request));
                break;
            default:
                throw new RestException('Uncaught response type');
                break;
        }
    }


    private function getAllHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = str_replace('_', '-', substr($key, 5));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }
}