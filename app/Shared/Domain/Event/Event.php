<?php

namespace App\Shared\Domain\Event;

abstract class Event
{
    abstract public function getPayload(): array;
}
