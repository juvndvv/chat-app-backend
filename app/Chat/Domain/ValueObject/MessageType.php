<?php

namespace App\Chat\Domain\ValueObject;

use App\Helpers\MessageTypeEnum;
use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\EnumValueObject;

final class MessageType extends EnumValueObject
{
    /**
     * @throws InvalidArgumentException
     */
    private function __construct(MessageTypeEnum $type)
    {
        parent::__construct($type->value);
    }

    public function isText(): bool
    {
        return $this->value === MessageTypeEnum::TEXT;
    }

    protected static function getValues(): array
    {
        return array_map(fn($case) => $case->value, MessageTypeEnum::cases());
    }

    public static function create(MessageTypeEnum $type): MessageType
    {
        return new self($type);

    }
}
