<?php

namespace App\Chat\Domain;

use App\Shared\Domain\Event\Event;

class UserSawChatRoomEvent extends Event
{
    public function __construct(
        private readonly string $user,
    )
    {
    }

    public function getPayload(): array
    {
        return [
            'user' => $this->user,
        ];
    }
}
