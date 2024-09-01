<?php

declare(strict_types = 1);

namespace App\Chat\Domain\ValueObject;

use App\Chat\Domain\Entity\AbstractMessage;
use App\Helpers\AbstractCustomCollection;
use LogicException;

final class ChatRoomMessageCollection extends AbstractCustomCollection
{
    public function ensureIsCorrectInstance(mixed $value) : void
    {
        if (!$value instanceof AbstractMessage) {
            throw new LogicException('$value must be instance of AbstractMessage');
        }
    }

    public function getValue(mixed $value): mixed
    {
        return $value->getId();
    }
}
