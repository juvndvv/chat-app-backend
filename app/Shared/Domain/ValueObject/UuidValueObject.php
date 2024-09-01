<?php

declare(strict_types = 1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use Random\RandomException;

/**
 * Class UuidValueObject
 *
 * Abstract base class for value objects representing UUIDs (Universally Unique Identifiers).
 * Ensures the UUID value is valid and provides methods for creating and generating UUIDs.
 *
 * @package App\Shared\Domain\ValueObject
 */
abstract class UuidValueObject
{
    /**
     * @var string The UUID value.
     */
    private readonly string $value;

    /**
     * UuidValueObject constructor.
     *
     * @param string $value The UUID value.
     *
     * @throws InvalidArgumentException If the provided value is not a valid UUID string.
     */
    public function __construct(string $value)
    {
        $this->ensureValueIsValid($value);
        $this->value = $value;
    }

    /**
     * Ensures that the UUID value is valid.
     *
     * This method validates that the UUID value has a length of 36 characters, which is the standard length for UUIDs.
     *
     * @throws InvalidArgumentException If the UUID value is not a valid UUID string.
     */
    public function ensureValueIsValid(string $value): void
    {
        if (strlen($value) !== 36) {
            throw new InvalidArgumentException(sprintf('Value "%s" is not a valid UUID string', $this->value));
        }
    }

    /**
     * Gets the UUID value.
     *
     * @return string The UUID value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Creates a new instance of the class with the given UUID value.
     *
     * @param string $uuid The UUID value.
     *
     * @return static A new instance of the class with the provided UUID value.
     *
     * @throws InvalidArgumentException If the provided value is not a valid UUID string.
     */
    public static function create(string $uuid): static
    {
        return new static($uuid);
    }

    /**
     * Generates a new UUID and returns an instance of the class with the generated value.
     *
     * The generated UUID is a version 4 UUID, which is randomly generated and formatted as a string.
     * If a prefix is provided, it will be prepended to the generated UUID.
     *
     * @param string|null $prefix Optional prefix to prepend to the generated UUID. Must be 2 characters long.
     *
     * @return static A new instance of the class with the generated UUID value.
     *
     * @throws RandomException If there is an error generating random bytes.
     * @throws InvalidArgumentException If the prefix is invalid or not exactly 2 characters long.
     */
    public static function generate(?string $prefix = null): static
    {
        if ($prefix !== null && strlen($prefix) !== 2) {
            throw new InvalidArgumentException(sprintf('Invalid UUID prefix "%s"', $prefix));
        }

        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // versión 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variante is DCE 1.1

        $formatedUuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

        return new static($formatedUuid);
    }
}
