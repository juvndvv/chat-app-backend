<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;

/**
 * Class PasswordValueObject
 *
 * Represents a value object for a password.
 * Ensures the password meets specific security requirements and provides methods for accessing the password.
 *
 * @package App\Shared\Domain\ValueObject
 */
abstract class PasswordValueObject
{
    /**
     * @var string The password value.
     */
    protected string $value;

    /**
     * Excluded chars for security
     *
     * @var array|string[]
     */
    protected array $excludedChars = ['\'', '"', ';', '--', '\\'];


    /**
     * Private constructor to enforce the use of the named constructor.
     *
     * @param string $value The password value.
     * @param int|null $minLength Minimum length for the password.
     * @param int|null $minChars Minimum number of characters required.
     * @param int|null $minUppercase Minimum number of uppercase letters required.
     * @param int|null $minSymbols Minimum number of symbols required.
     *
     * @throws InvalidArgumentException If the provided value does not meet the security requirements.
     */
    private function __construct(
        string $value,
        ?int   $minLength = null,
        ?int   $minChars = null,
        ?int   $minUppercase = null,
        ?int   $minSymbols = null,
    )
    {
        $this->ensureIsValid(
            $value,
            $minLength,
            $minChars,
            $minUppercase,
            $minSymbols
        );
        $this->value = $value;
    }

    /**
     * Ensures that the password value meets the specified security requirements.
     *
     * @param string $value The password value to validate.
     *
     * @throws InvalidArgumentException If the password does not meet the security requirements.
     */
    protected function ensureIsValid(
        string $value,
        ?int   $minLength = null,
        ?int   $minChars = null,
        ?int   $minUppercase = null,
        ?int   $minSymbols = null
    ): void {
        if (strlen($value) < $minLength) {
            throw new InvalidArgumentException("Password must be at least $minSymbols characters long.");
        }

        if (preg_match_all('/[A-Za-z]/', $value) < $minChars) {
            throw new InvalidArgumentException("Password must contain at least $minSymbols letters.");
        }

        if (preg_match_all('/[A-Z]/', $value) < $minUppercase) {
            throw new InvalidArgumentException("Password must contain at least $minSymbols uppercase letters.");
        }

        if (preg_match_all('/[!@#$%^&*(),.?":{}|<>]/', $value) < $minSymbols) {
            throw new InvalidArgumentException("Password must contain at least $minSymbols symbols.");
        }

        foreach ($this->excludedChars as $char) {
            if (str_contains($value, $char)) {
                throw new InvalidArgumentException("Password contains invalid characters.");
            }
        }
    }

    /**
     * Gets the value of the password.
     *
     * @return string The password value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Checks if two PasswordValueObjects are equal.
     *
     * @param PasswordValueObject $other The other password object to compare.
     *
     * @return bool True if both passwords are equal, false otherwise.
     */
    public function equals(PasswordValueObject $other): bool
    {
        return $this->value === $other->value();
    }

    /**
     * Named constructor to create a new instance of the class.
     *
     * @param string $value The password value.
     * @param int $minLength Minimum length for the password.
     * @param int $minChars Minimum number of characters required.
     * @param int $minUppercase Minimum number of uppercase letters required.
     * @param int $minSymbols Minimum number of symbols required.
     *
     * @return static A new instance of the class with the provided password value.
     *
     * @throws InvalidArgumentException If the provided value does not meet the security requirements.
     */
    public static function create(
        string $value,
        int    $minLength = 8,
        int    $minChars = 1,
        int    $minUppercase = 1,
        int    $minSymbols = 1
    ): static
    {
        return new static($value, $minLength, $minChars, $minUppercase, $minSymbols);
    }
}
