<?php

namespace App\Helpers;

use Closure;

abstract class AbstractCustomCollection
{
    protected array $list;

    public function __construct()
    {
        $this->list = [];
    }

    abstract public function ensureIsCorrectInstance(mixed $value): void;

    abstract public function getValue(mixed $value): mixed;

    public function set(array $list): void
    {
        foreach ($list as $item) {
            static::ensureIsCorrectInstance($item);
        }

        $this->list = $list;
    }

    public function append($value): void
    {
        static::ensureIsCorrectInstance($value);
        $this->list[] = $value;
    }

    public function prepend($value): void
    {
        static::ensureIsCorrectInstance($value);
        array_unshift($this->list, $value);
    }

    public function delete($value): bool
    {
        static::ensureIsCorrectInstance($value);

        for ($i = 0; $i < count($this->list); $i++) {
            if (static::getValue($this->list[$i]) === static::getValue($value)) {
                unset($this->list[$i]);
                return true;
            }
        }

        return false;
    }

    public function contains($value): bool
    {
        static::ensureIsCorrectInstance($value);

        foreach ($this->list as $item) {
            if (static::getValue($item) === static::getValue($value)) {
                return true;
            }
        }

        return false;
    }

    public function count(): int
    {
        return count($this->list);
    }

    public function forEach(Closure $closure): void
    {
        foreach ($this->list as $item) {
            $closure($item);
        }
    }

    public function map(Closure $closure): array
    {
        $mapped = [];

        foreach ($this->list as $item) {
            $mapped[] = $closure($item);
        }

        return $mapped;
    }

    public function filter(Closure $closure): array
    {
        return array_filter($this->list, $closure);
    }
}
