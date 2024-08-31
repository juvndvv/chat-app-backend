<?php

declare(strict_types=1);

namespace App\Chat\Domain\Entity;

use App\Chat\Domain\Exception\MessageCreationException;
use App\Chat\Domain\ValueObject\MessageContent;
use App\Chat\Domain\ValueObject\MessageId;
use App\Shared\Domain\Entity\Entity;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\DateTimeValueObject;
use App\User\Domain\Exception\UserCreationException;
use App\User\Domain\ValueObject\UserId;
use DateTimeImmutable;
use Random\RandomException;

final class TextMessage extends Entity
{
    private MessageId $id;
    private UserId $userId;
    private MessageContent $content;
    private DateTimeValueObject $createdAt;
    private DateTimeValueObject $updatedAt;

    public function __construct(
        ?string            $id = null,
        ?string            $userId = null,
        ?string            $content = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    )
    {
        if ($id !== null) $this->id = MessageId::create($id);
        if ($userId !== null) $this->userId = UserId::create($userId);
        if ($content !== null) $this->content = MessageContent::create($content);
        if ($createdAt !== null) $this->createdAt = DateTimeValueObject::create($createdAt);
        if ($updatedAt !== null) $this->updatedAt = DateTimeValueObject::create($updatedAt);
    }

    public function getId(): string
    {
        if (!isset($this->id)) {
            throw new LogicException('Message\'s id is not set');
        }

        return $this->id->value();
    }

    public function getUserId(): string
    {
        if (!isset($this->userId)) {
            throw new LogicException('Message\'s userId is not set');
        }

        return $this->userId->value();
    }

    public function getContent(): string
    {
        if (!isset($this->content)) {
            throw new LogicException('Message\'s content is not set');
        }

        return $this->content->value();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        if (!isset($this->createdAt)) {
            throw new LogicException('Message\'s createdAt is not set');
        }

        return $this->createdAt->value();
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        if (!isset($this->updatedAt)) {
            throw new LogicException('Message\'s updatedAt is not set');
        }

        return $this->updatedAt->value();
    }

    public function setId(string $id): self
    {
        $this->id = MessageId::create($id);
        return $this;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = UserId::create($userId);
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = MessageContent::create(trim($content));
        return $this;
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

    public function updateContent(string $content): void
    {
        $this->setContent($content);
    }

    private function performUpdate(): void
    {
        $this->updatedAt = DateTimeValueObject::create(new DateTimeImmutable());
    }

    public static function build(): self
    {
        return new self();
    }

    public static function create(
        string $userId,
        string $text,
        int    $try = 1
    ): self
    {
        try {
            $now = new DateTimeImmutable();

            return self::build()
                ->setId(MessageId::generate()->value())
                ->setUserId($userId)
                ->setContent($text)
                ->setCreatedAt($now)
                ->setUpdatedAt($now);

            // TODO create event

        } catch (RandomException $e) {
            if ($try === 3) {
                throw new MessageCreationException('Failed to generate message ID after multiple attempts: ' . $e->getMessage());
            }

            $try += 1;
            return self::create($userId, $text, $try);
        }
    }
}
