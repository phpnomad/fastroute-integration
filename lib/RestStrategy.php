<?php

namespace PHPNomad\Fastroute\Component;

use PHPNomad\Fastroute\Component\Registries\RestRoutesRegistry;
use PHPNomad\Rest\Interfaces\Controller;
use PHPNomad\Rest\Interfaces\HasInterceptors;
use PHPNomad\Rest\Interfaces\HasMiddleware;
use PHPNomad\Rest\Interfaces\HasValidations;
use PHPNomad\Rest\Interfaces\Response;
use PHPNomad\Rest\Interfaces\RestStrategy as CoreRestStrategy;

class RestStrategy implements CoreRestStrategy
{

    public function __construct(protected RestRoutesRegistry $registry)
    {

    }

    public function registerRoute(callable $controllerGetter)
    {
        $this->registry->set(function () use ($controllerGetter) {
            /** @var Controller $controller */
            $controller = $controllerGetter();

            return [
                $controller->getMethod(),
                $controller->getEndpoint(),
                function ($request) use ($controller) {
                    if ($controller instanceof HasValidations) {
                        $this->runValidations($controller, $request);
                    }
                    if ($controller instanceof HasMiddleware) {
                        $this->runMiddleware($controller, $request);
                    }
                    $response = $controller->getResponse($request);
                    $this->setRestHeaders($response);

                    if ($controller instanceof HasInterceptors) {
                        $this->runInterceptors($controller, $request, $response);
                    }

                    return $response;
                }
            ];

        });
    }

    /**
     * Sets default headers for REST responses on the provided Response object.
     *
     * @param Response $response The response object to which headers are added.
     */
    private function setRestHeaders(Response $response): void
    {
        // Set Content-Type header to JSON, which is typical for REST responses
        $response->setHeader('Content-Type', 'application/json');

        // Set CORS headers to allow cross-origin requests
        $response->setHeader('Access-Control-Allow-Origin', '*'); // Adjust as needed
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS'); // Allowed HTTP methods
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization'); // Allowed headers

        // Set caching headers to prevent caching, if needed
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Expires', '0');

        // Add any other necessary headers here
    }

    private function runValidations(Controller $controller, $request): void
    {
        /**
         * @TODO
         *
         * This should run preflight validations based on the configurations sometimes found in the controller.
         * If a controller implements HasValidations, we should implement it here.
         *
         * See WordPress integration for more info.
         */
    }

    private function runMiddleware(Controller $controller, $request): void
    {
        /**
         * @TODO
         *
         * This should run middleware based on the configurations sometimes found in the controller.
         * If a controller implements HasMiddleware, we should implement it here.
         *
         * See WordPress integration for more info.
         */
    }

    private function runInterceptors(Controller $controller, $request, $response): void
    {
        /**
         * @TODO
         *
         * This should run interceptors based on the configurations sometimes found in the controller.
         * If a controller implements HasInterceptors, we should implement it here.
         *
         * See WordPress integration for more info.
         */
    }
}