<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;

/**
 * Class IntegerValueObject
 *
 * Abstract base class for value objects representing an integer.
 * Ensures the integer value is valid and provides methods for accessing and comparing the value.
 *
 * @package App\Shared\Domain\ValueObject
 */
abstract class IntegerValueObject
{
    /**
     * @var int The integer value of the value object.
     */
    protected int $value;

    /**
     * IntegerValueObject constructor.
     *
     * @param int $value The integer value.
     *
     * @throws InvalidArgumentException If the provided value is not a valid integer.
     */
    private function __construct(int $value, ?int $min = null, ?int $max = null)
    {
        $this->ensureIsValid($value, $min, $max);
        $this->value = $value;
    }

    /**
     * Ensures that the value is valid.
     *
     * This method can be overridden by subclasses to provide specific validation rules.
     *
     * @param int $value The value to validate.
     *
     * @throws InvalidArgumentException
     */
    protected function ensureIsValid(int $value, ?int $min, ?int $max): void
    {
        if ($value <= $min && $value >= $max) {
            throw new InvalidArgumentException(sprintf('Value "%s" is out of range [%s, %s]', $value, $min, $max));
        }

    }

    /**
     * Gets the value of the object as an integer.
     *
     * @return int The integer value of the object.
     */
    public function value(): int
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
        return (string) $this->value;
    }

    /**
     * Checks if two value objects are equal.
     *
     * @param IntegerValueObject $other The other value object to compare.
     *
     * @return bool True if both value objects have the same value, false otherwise.
     */
    public function equals(IntegerValueObject $other): bool
    {
        return $this->value === $other->value();
    }

    /**
     * Named constructor for IntegerValueObject
     *
     * @param int $value
     * @param int|null $min
     * @param int|null $max
     * @return IntegerValueObject
     * @throws InvalidArgumentException
     */
    public static function create(int $value, ?int $min = null, ?int $max = null): static
    {
        return new static($value, $min, $max);
    }
}
