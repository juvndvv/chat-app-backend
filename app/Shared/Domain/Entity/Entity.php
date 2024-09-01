<?php

namespace App\Shared\Domain\Entity;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\DateTimeValueObject;
use Closure;

abstract class Entity
{
    protected array $events = [];

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
