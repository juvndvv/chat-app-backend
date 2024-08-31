<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;

/**
 * Class BooleanValueObject
 *
 * Abstract base class for value objects representing a boolean value.
 * Ensures the boolean value is valid and provides methods for accessing and comparing the value.
 *
 * @package App\Shared\Domain\ValueObject
 */
abstract class BoolValueObject
{
    /**
     * @var bool The boolean value of the value object.
     */
    protected bool $value;

    /**
     * Private constructor to enforce the use of the named constructor.
     *
     * @param bool $value The boolean value.
     *
     */
    private function __construct(bool $value)
    {
        $this->ensureIsValid($value);
        $this->value = $value;
    }

    /**
     * Ensures that the value is valid.
     *
     * This method can be overridden by subclasses to provide specific validation rules.
     *
     * @param bool $value The value to validate.
     *
     */
    protected function ensureIsValid(bool $value): void
    {
        // Default validation: all boolean values are considered valid.
        // Subclasses can override this to enforce specific rules if necessary.
    }

    /**
     * Gets the value of the object as a boolean.
     *
     * @return bool The boolean value of the object.
     */
    public function value(): bool
    {
        return $this->value;
    }

    /**
     * Converts the object to a string.
     *
     * @return string The string representation of the boolean value.
     */
    public function __toString(): string
    {
        return $this->value ? 'true' : 'false';
    }

    /**
     * Checks if two value objects are equal.
     *
     * @param BoolValueObject $other The other value object to compare.
     *
     * @return bool True if both value objects have the same boolean value, false otherwise.
     */
    public function equals(BoolValueObject $other): bool
    {
        return $this->value === $other->value();
    }

    /**
     * Named constructor to create a new instance of the class.
     *
     * @param bool $value The boolean value.
     *
     * @return static A new instance of the class with the provided boolean value.
     *
     */
    public static function create(bool $value): static
    {
        return new static($value);
    }
}
