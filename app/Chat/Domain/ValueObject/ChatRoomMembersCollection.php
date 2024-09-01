<?php

declare(strict_types = 1);

namespace App\Chat\Domain\ValueObject;

use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\AbstractCustomCollection;
use App\User\Domain\ValueObject\UserId;

final class ChatRoomMembersCollection extends AbstractCustomCollection
{
    public function ensureIsCorrectInstance(mixed $value): void
    {
        if (!$value instanceof UserId) {
            throw new LogicException('$value must be instance of UserId');
        }
    }

    public function getValue(mixed $value): mixed
    {
        return $value->value();
    }
}
