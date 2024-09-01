<?php

declare(strict_types = 1);

namespace App\Chat\Domain\ValueObject;

use App\Shared\Domain\ValueObject\DateTimeValueObject;
use DateTimeImmutable;

final class OptionalDateTimeValueObject extends DateTimeValueObject
{
    protected function __construct(?DateTimeImmutable $value)
    {
        if ($value !== null) {
            parent::__construct($value);
        }
    }

    public function isNull(): bool
    {
        return !$this->value();
    }

    public function isNotNull(): bool
    {
        return !$this->isNull();
    }

    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    public static function create(?DateTimeImmutable $value): OptionalDateTimeValueObject
    {
        return new self($value);
    }
}
