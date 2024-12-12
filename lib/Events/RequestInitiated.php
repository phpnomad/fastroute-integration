<?php

namespace PHPNomad\Fastroute\Component\Events;

use PHPNomad\Events\Interfaces\Event;
use PHPNomad\Rest\Interfaces\Response;

class RequestInitiated implements Event
{
    protected Response $response;

    public function __construct(public readonly string $method, public readonly string $uri)
    {

    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    public static function getId(): string
    {
        return 'nomadic_request_initiated';
    }
}