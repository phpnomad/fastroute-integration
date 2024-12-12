<?php

namespace PHPNomad\Fastroute\Component\Interfaces;

use PHPNomad\Rest\Interfaces\Response;

interface WebController
{
    /**
     * Modifies the provided response, adding the body.
     *
     * @param Response $response
     * @return Response
     */
    public function response(Response $response): Response;
}