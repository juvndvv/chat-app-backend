<?php

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\Port\ControllerPort;
use Illuminate\Http\Request;

abstract class HttpControllerPort implements ControllerPort
{
    abstract public function __invoke(Request $request);
}
