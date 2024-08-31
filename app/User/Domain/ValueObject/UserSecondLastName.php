<?php

namespace App\User\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\StringValueObject;

final class UserSecondLastName extends StringValueObject
{
    private const MIN_LENGTH = 1;
    private const MAX_LENGTH = 255;

    private function __construct(?string $value)
    {
        $this->ensureIsValid($value);
        $value === null ?: parent::__construct($value, self::MIN_LENGTH, self::MAX_LENGTH);
    }

    public function value(): string
    {
        return $this->value ?? '';
    }

    public function isNull(): bool
    {
        return !isset($this->value);
    }

    public function isNotNull(): bool
    {
        return !$this->isNull();
    }

    protected function ensureIsValid(?string $value): void
    {
        if ($value !== null && empty($value)) {
            throw new InvalidArgumentException("Value cannot be empty.");
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function create(?string $value): self
    {
        return new self($value);
    }
}
