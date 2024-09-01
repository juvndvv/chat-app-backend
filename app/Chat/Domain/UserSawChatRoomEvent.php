<?php

namespace App\Chat\Domain;

use App\Shared\Domain\Event\Event;
use DateTimeImmutable;

class UserSawChatRoomEvent extends Event
{
    public function __construct(
        private readonly string $user,
        private readonly DateTimeImmutable $createdAt,
    )
    {
    }

    public function getPayload(): array
    {
        return [
            'user' => $this->user,
            'at' => $this->createdAt->format(Event::DATE_FORMAT),
        ];
    }
}
