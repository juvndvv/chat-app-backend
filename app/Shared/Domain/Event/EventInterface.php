<?php

namespace App\Shared\Domain\Event;

interface EventInterface
{
    public function getPayload(): array;
}
