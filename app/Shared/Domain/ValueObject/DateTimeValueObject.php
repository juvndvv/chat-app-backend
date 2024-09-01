<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use DateTimeImmutable;

/**
 * Class DateTimeValueObject
 *
 * Abstract base class for value objects representing a DateTime value.
 * Ensures the DateTime value is valid and provides methods for accessing, comparing, and creating DateTime values.
 *
 * @package App\Shared\Domain\ValueObject
 */
class DateTimeValueObject
{
    public const VIEW_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var DateTimeImmutable The DateTime value of the value object.
     */
    protected DateTimeImmutable $value;

    /**
     * Private constructor to enforce the use of the named constructor.
     *
     * @param DateTimeImmutable $value The DateTime value.
     *
     * @throws InvalidArgumentException If the provided value is not a valid DateTime.
     */
    protected function __construct(DateTimeImmutable $value)
    {
        $this->ensureIsValid($value);
        $this->value = $value;
    }

    /**
     * Ensures that the DateTime value is valid.
     *
     * This method can be overridden by subclasses to provide specific validation rules.
     *
     * @param DateTimeImmutable $value The DateTime value to validate.
     *
     * @throws InvalidArgumentException If the value is not valid.
     */
    protected function ensureIsValid(DateTimeImmutable $value): void
    {
        // Default validation: any DateTimeImmutable value is considered valid.
        // Subclasses can override this to enforce specific rules if necessary.
    }

    /**
     * Gets the value of the object as a DateTimeImmutable.
     *
     * @return DateTimeImmutable The DateTime value of the object.
     */
    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    /**
     * Converts the object to a string in ISO 8601 format.
     *
     * @return string The string representation of the DateTime value.
     */
    public function __toString(): string
    {
        return $this->value->format(self::VIEW_FORMAT);
    }

    /**
     * Checks if two value objects are equal.
     *
     * @param DateTimeValueObject $other The other value object to compare.
     *
     * @return bool True if both value objects have the same DateTime value, false otherwise.
     */
    public function equals(DateTimeValueObject $other): bool
    {
        return $this->value->format(self::VIEW_FORMAT) === $other->value()->format(self::VIEW_FORMAT);
    }

    /**
     * Compares the current DateTimeValueObject with another DateTimeValueObject.
     *
     * @param DateTimeValueObject $other The other value object to compare.
     *
     * @return int Returns -1 if the current value is earlier than the other value, 1 if it is later, and 0 if they are equal.
     */
    public function compare(DateTimeValueObject $other): int
    {
        return $this->value <=> $other->value();
    }

    /**
     * Named constructor to create a new instance of the class.
     *
     * @param DateTimeImmutable $value The DateTime value.
     *
     * @return static A new instance of the class with the provided DateTime value.
     *
     * @throws InvalidArgumentException If the provided value is not valid.
     */
    public static function create(DateTimeImmutable $value): static
    {
        return new static($value);
    }

    /**
     * Named constructor to create a new instance at the start of the day.
     *
     * @param DateTimeImmutable $date The date to use for creating the DateTime value (time will be set to 00:00:00).
     *
     * @return static A new instance of the class representing the start of the day.
     * @throws InvalidArgumentException
     */
    public static function atStartOfDay(DateTimeImmutable $date): static
    {
        $startOfDay = $date->setTime(0, 0, 0);
        return new static($startOfDay);
    }

    /**
     * Named constructor to create a new instance at the end of the day.
     *
     * @param DateTimeImmutable $date The date to use for creating the DateTime value (time will be set to 23:59:59).
     *
     * @return static A new instance of the class representing the end of the day.
     * @throws InvalidArgumentException
     */
    public static function atEndOfDay(DateTimeImmutable $date): static
    {
        $endOfDay = $date->setTime(23, 59, 59);
        return new static($endOfDay);
    }
}
