<?php

namespace App\Helpers;

use Closure;

final class ValueObjectList
{
    private array $list;

    public function __construct()
    {
        $this->list = [];
    }

    public function set(array $list): void
    {
        $this->list = $list;
    }

    public function append($value)
    {
        $this->list[] = $value;
    }

    public function prepend($value)
    {
        array_unshift($this->list, $value);
    }

    public function delete($value): bool
    {
        foreach ($this->list as $item) {
            if ($item->value() == $value) {
                unset($this->list[$item]);
                return true;
            }
        }

        return false;
    }

    public function count(): int
    {
        return count($this->list);
    }

    public function contains($value): bool
    {
        foreach ($this->list as $item) {
            if ($item->value() == $value) {
                return true;
            }
        }

        return false;
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
