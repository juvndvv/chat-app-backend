<?php

namespace App\Shared\Domain\Port;

use Illuminate\Http\Request;

interface ControllerPort
{
    public function __invoke(Request $request);
    public function validate(Request $request);
}
