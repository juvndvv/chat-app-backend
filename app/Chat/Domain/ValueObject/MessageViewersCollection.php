<?php

declare(strict_types = 1);

namespace App\Chat\Domain\ValueObject;

use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\AbstractCustomCollection;
use App\Shared\Domain\ValueObject\DateTimeValueObject;
use App\User\Domain\ValueObject\UserId;

final class MessageViewersCollection extends AbstractCustomCollection
{
    /**
     * @throws LogicException
     */
    public function ensureIsCorrectInstance(mixed $value): void
    {
        if (!is_array($value)) {
            throw new LogicException('$value must be an array');
        }

        if (!isset($value[0]) && !$value[0] instanceof UserId) {
            throw new LogicException('$value[0] must be an instance of UserId');
        }

        if (!isset($value[1]) && !$value[1] instanceof DateTimeValueObject) {
            throw new LogicException('$value[1] must be an instance of DateTimeValueObject');
        }
    }

    public function getValue(mixed $value): mixed
    {
        return $value[0]->value();
    }
}
