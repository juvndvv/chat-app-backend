<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\Shared\Domain\Entity\Entity;
use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\DateTimeValueObject;
use App\User\Domain\Exception\UserCreationException;
use App\User\Domain\ValueObject\UserCanExecCommands;
use App\User\Domain\ValueObject\UserEmail;
use App\User\Domain\ValueObject\UserFirstLastName;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use App\User\Domain\ValueObject\UserSecondLastName;
use DateTimeImmutable;
use Random\RandomException;

final class User extends Entity
{
    private UserId $id;
    private UserName $name;
    private UserFirstLastName $firstLastName;
    private UserSecondLastName $secondLastName;
    private UserEmail $email;
    private UserCanExecCommands $canExecCommands;
    private DateTimeValueObject $createdAt;
    private DateTimeValueObject $updatedAt;

    /**
     * Sets the user's unique identifier.
     *
     * @param string $id The user's ID.
     * @return self
     *
     * @throws InvalidArgumentException If the ID is invalid.
     */
    public function setId(string $id): self
    {
        $this->id = UserId::create($id);
        return $this;
    }

    /**
     * Sets the user's first name.
     *
     * @param string $name The user's first name.
     * @return self
     *
     * @throws InvalidArgumentException If the name is invalid.
     */
    public function setName(string $name): self
    {
        $this->name = UserName::create(trim($name));
        return $this;
    }

    /**
     * Sets the user's first last name.
     *
     * @param string $firstLastName The user's first last name.
     * @return self
     *
     * @throws InvalidArgumentException If the first last name is invalid.
     */
    public function setFirstLastName(string $firstLastName): self
    {
        $this->firstLastName = UserFirstLastName::create(trim($firstLastName));
        return $this;
    }

    /**
     * Sets the user's second last name.
     *
     * @param string|null $secondLastName The user's second last name (optional).
     * @return self
     *
     * @throws InvalidArgumentException If the second last name is invalid.
     */
    public function setSecondLastName(?string $secondLastName): self
    {
        if ($secondLastName !== null) {
            $secondLastName = trim($secondLastName);
        }

        $this->secondLastName = UserSecondLastName::create($secondLastName);
        return $this;
    }

    /**
     * Sets the user's email address.
     *
     * @param string $email The user's email.
     * @return self
     *
     * @throws InvalidArgumentException If the email is invalid.
     */
    public function setEmail(string $email): self
    {
        $this->email = UserEmail::create(trim($email));
        return $this;
    }

    /**
     * Set's the user can exec command flag.
     *
     * @param bool $canExecCommands
     * @return self
     */
    public function setCanExecCommands(bool $canExecCommands): self
    {
        $this->canExecCommands = UserCanExecCommands::create($canExecCommands);
        return $this;
    }

    /**
     * Sets the user's creation timestamp.
     *
     * @param DateTimeImmutable $createdAt The creation timestamp.
     * @return self
     *
     * @throws InvalidArgumentException If the timestamp is invalid.
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = DateTimeValueObject::create($createdAt);
        return $this;
    }

    /**
     * Sets the user's last update timestamp.
     *
     * @param DateTimeImmutable $updatedAt The last update timestamp.
     * @return self
     *
     * @throws InvalidArgumentException If the timestamp is invalid.
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = DateTimeValueObject::create($updatedAt);
        return $this;
    }

    /*
     * GETTERS
     */

    /**
     * Gets the user's unique identifier.
     *
     * @return string The user's ID.
     * @throws LogicException If user's id is not set
     */
    public function getId(): string
    {
        if (!isset($this->id)) {
            throw new LogicException('User\'s id is not set');
        }

        return $this->id->value();
    }

    /**
     * Gets the user's first name.
     *
     * @return string The user's first name.
     * @throws LogicException If user's name is not set
     */
    public function getName(): string
    {
        if (!isset($this->name)) {
            throw new LogicException('User\'s name is not set');
        }

        return $this->name->value();
    }

    /**
     * Gets the user's first last name.
     *
     * @return string The user's first last name.
     * @throws LogicException If user's firstLastName is not set
     */
    public function getFirstLastName(): string
    {
        if (!isset($this->firstLastName)) {
            throw new LogicException('User\'s firstLastName is not set');
        }

        return $this->firstLastName->value();
    }

    /**
     * Gets the user's second last name, if set.
     *
     * @return string|null The user's second last name or null if not set.
     * @throws LogicException If user's secondLastName is not set
     */
    public function getSecondLastName(): ?string
    {
        if (!isset($this->secondLastName)) {
            throw new LogicException('User\'s secondLastName is not set');
        }

        return $this->secondLastName->isNotNull() ? $this->secondLastName->value() : null;
    }

    /**
     * Gets the user's full name.
     *
     * @return string The user's full name.
     * @throws LogicException If at least one of the needed properties is not set
     */
    public function getFullName(): string
    {
        $this->checkIfCanRetrieveFullName();

        return trim("{$this->name->value()} {$this->firstLastName->value()} {$this->secondLastName->value()}");
    }

    /**
     * Gets the user's email address.
     *
     * @return string The user's email.
     * @throws LogicException If user's email is not set
     */
    public function getEmail(): string
    {
        if (!isset($this->email)) {
            throw new LogicException('User\'s email is not set');
        }

        return $this->email->value();
    }

    /**
     * Gets the user's can exec commands flag
     *
     * @return bool The user's can exec commands flag
     * @throws LogicException If the flag is not set
     */
    public function canExecCommands(): bool
    {
        if (!isset($this->canExecCommands)) {
            throw new LogicException('User\'s canExecCommands is not set');
        }

        return $this->canExecCommands->value();
    }

    /**
     * Gets the timestamp when the user was created.
     *
     * @return DateTimeImmutable The creation timestamp.
     * @throws LogicException If user's createdAt is not set
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        if (!isset($this->createdAt)) {
            throw new LogicException('User\'s createdAt is not set');
        }

        return $this->createdAt->value();
    }

    /**
     * Gets the timestamp when the user was last updated.
     *
     * @return DateTimeImmutable The last update timestamp.
     * @throws LogicException If user's updatedAt is not set
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        if (!isset($this->updatedAt)) {
            throw new LogicException('User\'s updatedAt is not set');
        }

        return $this->updatedAt->value();
    }

    /*
     * UPDATE
     */

    /**
     * Updates the user's first name.
     *
     * @param string $name The user's new first name.
     * @param bool $isBulkUpdate Indicates if this is part of a bulk update.
     * @return self
     *
     * @throws InvalidArgumentException If the name is invalid.
     */
    public function updateName(string $name, bool $isBulkUpdate = false): self
    {
        $this->setName($name);

        if (!$isBulkUpdate) {
            $this->performUpdate();
            // TODO generate event
        }

        return $this;
    }

    /**
     * Updates the user's first last name.
     *
     * @param string $firstLastName The user's new first last name.
     * @param bool $isBulkUpdate Indicates if this is part of a bulk update.
     * @return self
     *
     * @throws InvalidArgumentException If the first last name is invalid.
     */
    public function updateFirstLastName(string $firstLastName, bool $isBulkUpdate = false): self
    {
        $this->setFirstLastName($firstLastName);

        if (!$isBulkUpdate) {
            $this->performUpdate();
            // TODO generate event
        }

        return $this;
    }

    /**
     * Updates the user's second last name.
     *
     * @param string|null $secondLastName The user's new second last name (optional).
     * @param bool $isBulkUpdate Indicates if this is part of a bulk update.
     * @return self
     *
     * @throws InvalidArgumentException If the second last name is invalid.
     */
    public function updateSecondLastName(?string $secondLastName, bool $isBulkUpdate = false): self
    {
        $this->setSecondLastName($secondLastName);

        if (!$isBulkUpdate) {
            $this->performUpdate();
            // TODO generate event
        }

        return $this;
    }

    /**
     * Updates the user's email address.
     *
     * @param string $email The user's new email address.
     * @param bool $isBulkUpdate Indicates if this is part of a bulk update.
     * @return self
     *
     * @throws InvalidArgumentException If the email is invalid.
     */
    public function updateEmail(string $email, bool $isBulkUpdate = false): self
    {
        $this->setEmail($email);

        if (!$isBulkUpdate) {
            $this->performUpdate();
            // TODO generate event
        }

        return $this;
    }

    /**
     * Updates the user's can exec commands flag.
     *
     * @param bool $canExecCommands The user's new value.
     * @param bool $isBulkUpdate Indicates if this is part of a bulk update.
     * @return self
     *
     * @throws InvalidArgumentException If the value is invalid.
     */
    public function updateCanExecCommands(bool $canExecCommands, bool $isBulkUpdate = false): self
    {
        $this->setCanExecCommands($canExecCommands);

        if (!$isBulkUpdate) {
            $this->performUpdate();
            // TODO generate event
        }

        return $this;
    }

    /**
     * Performs a bulk update of the user's attributes.
     *
     * @param string|null $name The user's new first name (optional).
     * @param string|null $firstLastName The user's new first last name (optional).
     * @param string|null $secondLastName The user's new second last name (optional).
     * @param string|null $email The user's new email address (optional).
     * @param bool|null $canExecCommands The user's can exec commands flag (optional).
     *
     * @throws InvalidArgumentException If all parameters are null or any value is invalid.
     */
    public function bulkUpdate(
        ?string $name = null,
        ?string $firstLastName = null,
        ?string $secondLastName = null,
        ?string $email = null,
        ?bool $canExecCommands = null,
    ): void
    {
        if ($this->allParametersAreNull(func_get_args())) {
            throw new InvalidArgumentException('All parameters cannot be null.');
        }

        if ($name !== null) $this->updateName($name, isBulkUpdate: true);
        if ($firstLastName !== null) $this->updateFirstLastName($firstLastName, isBulkUpdate: true);
        if ($secondLastName !== null) $this->updateSecondLastName($secondLastName, isBulkUpdate: true);
        if ($email !== null) $this->updateEmail($email, isBulkUpdate: true);
        if ($canExecCommands !== null) $this->updateCanExecCommands($canExecCommands, isBulkUpdate: true);

        $this->performUpdate();
    }

    /**
     * Updates the user's last update timestamp to the current time.
     *
     * @throws InvalidArgumentException If the timestamp is invalid.
     */
    private function performUpdate(): void
    {
        $this->updatedAt = DateTimeValueObject::create(new DateTimeImmutable());
    }

    /**
     * Checks if the necessary fields to retrieve the full name are set.
     *
     * This method verifies if the `name`, `firstLastName`, and `secondLastName` properties
     * are set. If any of these properties are not set, it will throw a LogicException
     * indicating which fields are missing.
     *
     * @return void
     * @throws LogicException If any of the required fields (`name`, `firstLastName`, `secondLastName`) are not set.
     *
     */
    private function checkIfCanRetrieveFullName(): void
    {
        $unsetFields = [];

        if (!isset($this->name)) {
            $unsetFields[] = 'name';
        }

        if (!isset($this->firstLastName)) {
            $unsetFields[] = 'firstLastName';
        }

        if (!isset($this->secondLastName)) {
            $unsetFields[] = 'secondLastName';
        }

        if (count($unsetFields) > 0) {
            throw new LogicException('Cannot retrieve full name without setting ' . implode(', ', $unsetFields));
        }
    }

    /**
     * Creates a new instance of the User entity with mandatory fields.
     *
     * @param string $name The user's name
     * @param string $firstLastName The user's first last name
     * @param string|null $secondLastName The user's second last name
     * @param string $email The user's email address.
     * @return self A new instance of the User entity.
     *
     * @throws InvalidArgumentException If any provided value is invalid.
     * @throws UserCreationException If uuid generator entropy fails 3 times
     */
    public static function create(
        string  $name,
        string  $firstLastName,
        ?string $secondLastName,
        string  $email,
        int     $try = 1
    ): self
    {
        try {
            $now = new DateTimeImmutable();

            return User::build()
                ->setId(UserId::generate()->value())
                ->setName($name)
                ->setFirstLastName($firstLastName)
                ->setSecondLastName($secondLastName)
                ->setEmail($email)
                ->setCanExecCommands(false) // default value
                ->setCreatedAt($now)
                ->setUpdatedAt($now);

            // TODO create event

        } catch (RandomException $e) {
            if ($try === 3) {
                throw new UserCreationException('Failed to generate user ID after multiple attempts: ' . $e->getMessage());
            }

            $try += 1;
            return self::create($name, $firstLastName, $secondLastName, $email, $try);
        }
    }
}
