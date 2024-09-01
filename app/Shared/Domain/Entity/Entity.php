<?php

namespace App\Shared\Domain\Entity;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\DateTimeValueObject;
use Closure;
use DateTimeImmutable;

abstract class Entity
{
    protected array $events = [];

    protected DateTimeValueObject $createdAt;
    protected DateTimeValueObject $updatedAt;
    protected DateTimeValueObject $deletedAt;

    public function getEvents(): array
    {
        return array_splice($this->events, 0);
    }

    protected function allParametersAreNull(...$params): bool
    {
        foreach ($params as $param) {
            if (!is_null($param)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Builds a new instance of the Entity without any preset values.
     *
     * @return self A new instance of the Entity.
     */
    public static function build(): static
    {
        return new static();
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = DateTimeValueObject::create($createdAt);
        return $this;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = DateTimeValueObject::create($updatedAt);
        return $this;
    }

    public function setDeletedAt(DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = DateTimeValueObject::create($deletedAt);
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        if (!isset($this->createdAt)) {
            throw new LogicException('The created at must be set');
        }

        return $this->createdAt->value();
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        if (!isset($this->updatedAt)) {
            throw new LogicException('The updated at must be set');
        }

        return $this->updatedAt->value();
    }

    public function getDeletedAt(): DateTimeImmutable
    {
        if (!isset($this->deletedAt)) {
            throw new LogicException('The deleted at must be set');
        }

        return $this->deletedAt->value();
    }

    /**
     * Updates the entity's last update timestamp to the current time.
     *
     * @throws InvalidArgumentException If the timestamp is invalid.
     */
    protected function performUpdate(): void
    {
        $this->updatedAt = DateTimeValueObject::create(new DateTimeImmutable());
    }
}
