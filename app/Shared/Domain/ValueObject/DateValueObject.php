<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use DateTimeImmutable;

/**
 * Class DateValueObject
 *
 * Represents a value object for a date (without time).
 * Ensures the date value is valid and provides methods for accessing, comparing, and creating Date values.
 *
 * @package App\Shared\Domain\ValueObject
 */
abstract class DateValueObject
{
    /**
     * @var DateTimeImmutable The Date value of the value object.
     */
    protected DateTimeImmutable $value;

    /**
     * Private constructor to enforce the use of the named constructor.
     *
     * @param DateTimeImmutable $value The Date value.
     *
     * @throws InvalidArgumentException If the provided value is not a valid Date.
     */
    private function __construct(DateTimeImmutable $value)
    {
        $this->ensureIsValid($value);
        $this->value = $value;
    }

    /**
     * Ensures that the Date value is valid.
     *
     * This method can be overridden by subclasses to provide specific validation rules.
     *
     * @param DateTimeImmutable $value The Date value to validate.
     *
     * @throws InvalidArgumentException If the value is not valid.
     */
    protected function ensureIsValid(DateTimeImmutable $value): void
    {
        // Default validation: any DateTimeImmutable value is considered valid as long as it represents a date.
        // Subclasses can override this to enforce specific rules if necessary.
    }

    /**
     * Gets the value of the object as a DateTimeImmutable.
     *
     * @return DateTimeImmutable The Date value of the object.
     */
    public function value(): DateTimeImmutable
    {
        return $this->value;
    }

    /**
     * Converts the object to a string in ISO 8601 format (date only).
     *
     * @return string The string representation of the Date value.
     */
    public function __toString(): string
    {
        return $this->value->format('Y-m-d'); // Date only format
    }

    /**
     * Checks if two value objects are equal.
     *
     * @param DateValueObject $other The other value object to compare.
     *
     * @return bool True if both value objects have the same Date value, false otherwise.
     */
    public function equals(DateValueObject $other): bool
    {
        return $this->value->format('Y-m-d') === $other->value()->format('Y-m-d');
    }

    /**
     * Compares the current DateValueObject with another DateValueObject.
     *
     * @param DateValueObject $other The other value object to compare.
     *
     * @return int Returns -1 if the current value is earlier than the other value, 1 if it is later, and 0 if they are equal.
     */
    public function compare(DateValueObject $other): int
    {
        return $this->value <=> $other->value();
    }

    /**
     * Named constructor to create a new instance of the class.
     *
     * @param DateTimeImmutable $value The Date value.
     *
     * @return static A new instance of the class with the provided Date value.
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
     * @param DateTimeImmutable $date The date to use for creating the Date value (time will be set to 00:00:00).
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
     * @param DateTimeImmutable $date The date to use for creating the Date value (time will be set to 23:59:59).
     *
     * @return static A new instance of the class representing the end of the day.
     * @throws InvalidArgumentException
     */
    public static function atEndOfDay(DateTimeImmutable $date): static
    {
        $endOfDay = $date->setTime(23, 59, 59);
        return new static($endOfDay);
    }

    /**
     * Named constructor to create a new instance from a specific date string.
     *
     * @param string $dateString The date string in 'Y-m-d' format.
     *
     * @return static A new instance of the class with the provided date.
     *
     * @throws InvalidArgumentException If the date string is not in the correct format.
     */
    public static function fromString(string $dateString): static
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $dateString);
        if ($date === false) {
            throw new InvalidArgumentException("Invalid date string format. Expected 'Y-m-d'.");
        }

        return new static($date);
    }

    /**
     * Named constructor to create a new instance from timestamp.
     *
     * @param int $timestamp The timestamp representing the date.
     *
     * @return static A new instance of the class with the provided timestamp.
     */
    public static function fromTimestamp(int $timestamp): static
    {
        $date = (new DateTimeImmutable())->setTimestamp($timestamp)->setTime(0, 0, 0);
        return new static($date);
    }
}
