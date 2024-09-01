<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;

abstract class EnumValueObject
{
    protected $value;

    protected function __construct($value)
    {
        if (!static::isValid($value)) {
            throw new InvalidArgumentException("Invalid value for " . static::class);
        }

        $this->value = $value;
    }

    public function value()
    {
        return $this->value->value();
    }

    abstract protected static function getValues(): array;

    public function __toString()
    {
        return (string) $this->value;
    }

    public function equals(EnumValueObject $other): bool
    {
        return $this->value === $other->getValue();
    }

    protected function isValid($value): bool
    {
        return in_array($value, static::getValues(), true);
    }

}
