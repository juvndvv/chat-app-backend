<?php

namespace App\Chat\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\StringValueObject;

final class MessageContent extends StringValueObject
{
    private const MIN_LENGTH = 1;
    private const MAX_LENGTH = 1500;

    /**
     * @throws InvalidArgumentException
     */
    public static function create(string $value): self
    {
        return parent::doCreate($value, self::MIN_LENGTH, self::MAX_LENGTH);
    }
}
