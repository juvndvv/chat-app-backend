<?php

declare(strict_types = 1);

namespace App\Chat\Domain\Entity;

use App\Chat\Domain\ValueObject\MessageId;
use App\Chat\Domain\ValueObject\MessageIsSent;
use App\Chat\Domain\ValueObject\MessageViewersCollection;
use App\Shared\Domain\Entity\Entity;
use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\DateTimeValueObject;
use App\User\Domain\ValueObject\UserId;
use DateTimeImmutable;

abstract class AbstractMessage extends Entity
{
    protected MessageId $id;
    protected UserId $userId;
    protected MessageIsSent $isSent;
    protected MessageViewersCollection $viewers;

    /**
     * Sets the unique identifier of the message.
     *
     * @param string $id The message ID.
     * @return self
     * @throws InvalidArgumentException If the ID is invalid.
     */
    public function setId(string $id): self
    {
        $this->id = MessageId::create($id);
        return $this;
    }

    /**
     * Sets the user ID who sent the message.
     *
     * @param string $userId The user ID.
     * @return self
     * @throws InvalidArgumentException If the user ID is invalid.
     */
    public function setUserId(string $userId): self
    {
        $this->userId = UserId::create($userId);
        return $this;
    }

    public function setViewers(array $viewers): self
    {
        $this->viewers = new MessageViewersCollection();

        foreach ($viewers as $viewer) {
            $this->viewers->append([
                UserId::create($viewer[0]),
                DateTimeValueObject::create($viewer[1])
            ]);
        }

        return $this;
    }

    public function setIsSent(bool $value): self
    {
        $this->isSent = MessageIsSent::create($value);
        return $this;
    }

    /**
     * Gets the unique identifier of the message.
     *
     * @return string The message ID.
     * @throws LogicException If the message ID is not set.
     */
    public function getId(): string
    {
        if (!isset($this->id)) {
            throw new LogicException('Message\'s id is not set');
        }

        return $this->id->value();
    }

    /**
     * Gets the user ID who sent the message.
     *
     * @return string The user ID.
     * @throws LogicException If the user ID is not set.
     */
    public function getUserId(): string
    {
        if (!isset($this->userId)) {
            throw new LogicException('Message\'s userId is not set');
        }

        return $this->userId->value();
    }

    public function getViewers(): array
    {
        return $this->viewers->map(function ($viewers) {
            return [
                'userId' => $viewers[0]->value(),
                'at' => $viewers[1]->value(),
            ];
        });
    }

    public function isSent(): bool
    {
        if (!isset($this->isSent)) {
            throw new LogicException('Message\'s isSent is not set');
        }

        return $this->isSent->value();
    }

    public function markAsViewed(string $userId): void
    {
        if (!isset($this->viewers)) {
            throw new LogicException('Message\'s viewers is not set');
        }

        $this->viewers->append([UserId::create($userId), DateTimeValueObject::create(new DateTimeImmutable())]);
    }


}
