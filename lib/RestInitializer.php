<?php

namespace PHPNomad\Fastroute\Component;

use PHPNomad\Di\Interfaces\CanSetContainer;
use PHPNomad\Di\Traits\HasSettableContainer;
use PHPNomad\Events\Interfaces\HasListeners;
use PHPNomad\Fastroute\Component\Events\RequestInitiated;
use PHPNomad\Fastroute\Component\Listeners\DispatchRequest;
use PHPNomad\Loader\Interfaces\HasClassDefinitions;

class RestInitializer implements HasClassDefinitions, CanSetContainer, HasListeners
{
    use HasSettableContainer;

    public function getClassDefinitions() : array
    {
        return [
          RestStrategy::class => \PHPNomad\Rest\Interfaces\RestStrategy::class,
          Request::class      => \PHPNomad\Rest\Interfaces\Request::class,
          Response::class     => \PHPNomad\Rest\Interfaces\Response::class,
        ];
    }

    public function getListeners() : array
    {
        return [
          RequestInitiated::class => DispatchRequest::class,
        ];
    }
}