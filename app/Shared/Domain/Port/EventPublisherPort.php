<?php

namespace App\Shared\Domain\Port;

use App\Shared\Domain\Event\Event;

interface EventPublisherPort
{
    /**
     * @param array<Event> $events
     * @return void
     */
    public function publish(array $events): void;
}
