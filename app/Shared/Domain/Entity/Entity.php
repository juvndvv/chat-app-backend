<?php

namespace App\Shared\Domain\Entity;

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
}
