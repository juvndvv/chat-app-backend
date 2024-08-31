<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;

/**
 * Class EmailValueObject
 *
 * Represents a value object for an email address.
 * Ensures the email value is valid and provides methods for accessing and comparing email addresses.
 *
 * @package App\Shared\Domain\ValueObject
 */
abstract class EmailValueObject
{
    /**
     * @var string The email address value.
     */
    protected string $value;

    /**
     * Private constructor to enforce the use of the named constructor.
     *
     * @param string $value The email address.
     *
     * @throws InvalidArgumentException If the provided value is not a valid email address.
     */
    private function __construct(string $value)
    {
        $this->ensureIsValid($value);
        $this->value = $value;
    }

    /**
     * Ensures that the email address value is valid.
     *
     * @param string $value The email address to validate.
     *
     * @throws InvalidArgumentException If the email address is not valid.
     */
    protected function ensureIsValid(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: $value");
        }
    }

    /**
     * Gets the value of the email address.
     *
     * @return string The email address value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Converts the object to a string.
     *
     * @return string The string representation of the email address.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Checks if two EmailValueObjects are equal.
     *
     * @param EmailValueObject $other The other value object to compare.
     *
     * @return bool True if both email addresses are equal, false otherwise.
     */
    public function equals(EmailValueObject $other): bool
    {
        return strtolower($this->value) === strtolower($other->value());
    }

    /**
     * Named constructor to create a new instance of the class.
     *
     * @param string $value The email address.
     *
     * @return static A new instance of the class with the provided email address.
     *
     * @throws InvalidArgumentException If the provided email address is not valid.
     */
    public static function create(string $value): static
    {
        return new static($value);
    }
}
