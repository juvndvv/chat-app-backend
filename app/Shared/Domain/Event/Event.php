<?php

namespace App\Shared\Domain\Event;

abstract class Event
{
    public const DATE_FORMAT = 'Y-m-d H:i';
    abstract public function getPayload(): array;
}
