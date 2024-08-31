<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;

/**
 * Class StringValueObject
 *
 * Abstract base class for value objects representing a string.
 * Ensures the string value is valid and provides methods for accessing and comparing the value.
 *
 * @package App\Shared\Domain\ValueObject
 */
abstract class StringValueObject
{
    /**
     * @var string The string value of the value object.
     */
    protected string $value;

    /**
     * StringValueObject constructor.
     *
     * @param string $value The string value.
     *
     * @throws InvalidArgumentException If the provided value is not valid.
     */
    protected function __construct(string $value, ?int $minLength = null, ?int $maxLength = null)
    {
        $this->ensureIsValid($value);
        $this->value = $value;
    }

    /**
     * Ensures that the value is valid.
     *
     * This method can be overridden by subclasses to provide specific validation rules.
     *
     * @param string $value The value to validate.
     *
     * @throws InvalidArgumentException If the value is not valid.
     */
    protected function ensureIsValid(string $value): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException("Value cannot be empty.");
        }
    }

    /**
     * Gets the value of the object as a string.
     *
     * @return string The value of the object.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Converts the object to a string.
     *
     * @return string The string representation of the object.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Checks if two value objects are equal.
     *
     * @param StringValueObject $other The other value object to compare.
     *
     * @return bool True if both value objects have the same value, false otherwise.
     */
    public function equals(StringValueObject $other): bool
    {
        return static::value() === $other->value();
    }

    /**
     * Named constructor for StringValueObject
     *
     * @param string $value
     * @param int|null $min
     * @param int|null $max
     * @return StringValueObject
     * @throws InvalidArgumentException
     */
    public static function doCreate(string $value, ?int $min = null, ?int $max = null): static
    {
        return new static($value);
    }
}
