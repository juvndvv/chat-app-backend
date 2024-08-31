<?php

namespace App\Shared\Domain\Service;

use App\Shared\Domain\Port\EventPublisherPort;

final class RedisEventPublisher implements EventPublisherPort
{
    public function publish(array $events): void
    {
        // TODO: Implement publish() method.
    }
}
