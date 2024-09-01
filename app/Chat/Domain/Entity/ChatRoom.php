<?php

declare(strict_types=1);

namespace App\Chat\Domain\Entity;

use App\Chat\Domain\Exception\ChatRoomCannotBeEmptyException;
use App\Chat\Domain\ValueObject\ChatRoomDescription;
use App\Chat\Domain\ValueObject\ChatRoomId;
use App\Chat\Domain\ValueObject\ChatRoomName;
use App\Chat\Domain\ValueObject\MessageId;
use App\Helpers\ValueObjectList;
use App\Shared\Domain\Entity\Entity;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\Exception\MessageAlreadyInChatException;
use App\Shared\Domain\Exception\UserAlreadyInChatRoomException;
use App\Shared\Domain\Exception\UserDoesNotPertainsToChatRoomException;
use App\Shared\Domain\ValueObject\DateTimeValueObject;
use App\User\Domain\ValueObject\UserId;
use DateTimeImmutable;

final class ChatRoom extends Entity
{
    private ChatRoomId $id;
    private ChatRoomName $name;
    private ChatRoomDescription $description;
    private ValueObjectList $members;
    private ValueObjectList $messages;
    private UserId $creatorId;
    private DateTimeValueObject $createdAt;
    private DateTimeValueObject $updatedAt;
    private DateTimeValueObject $deletedAt;

    public function setId(string $id): self
    {
        $this->id = ChatRoomId::create($id);
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = ChatRoomName::create($name);
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = ChatRoomDescription::create($description);
        return $this;
    }

    public function setMembers(array $members): self
    {
        if (count($members) === 0) {
            throw new ChatRoomCannotBeEmptyException();
        }

        $this->members = new ValueObjectList();

        $this->members->set(array_map(function (string $member) {
            return UserId::create($member);
        }, $members));

        return $this;
    }

    public function setMessages(array $messages): self
    {
        $this->messages = new ValueObjectList();
        $this->messages->set($messages);
        return $this;
    }

    public function setCreatorId(string $creatorId): self
    {
        $this->creatorId = UserId::create($creatorId);
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

    public function setDeletedAt(DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = DateTimeValueObject::create($deletedAt);
        return $this;
    }

    public function getId(): string
    {
        if (!isset($this->id)) {
            throw new LogicException('ChatRoom\'s id is not set');
        }

        return $this->id->value();
    }

    public function getName(): string
    {
        if (!isset($this->name)) {
            throw new LogicException('ChatRoom\'s name is not set');
        }

        return $this->name->value();
    }

    public function getDescription(): string
    {
        if (!isset($this->description)) {
            throw new LogicException('ChatRoom\'s description is not set');
        }

        return $this->description->value();
    }

    public function getMembers(): array
    {
        if (!isset($this->members)) {
            throw new LogicException('ChatRoom\'s members has not been set');
        }

        $members = $this->members->map(function (UserId $member) {
            return $member->value();
        });

        array_unshift($members, $this->creatorId->value());

        return $members;
    }

    public function getMembersCount(): int
    {
        if (!isset($this->members)) {
            throw new LogicException('ChatRoom\'s members has not been set');
        }

        return $this->members->count();
    }

    public function getMessages(): array
    {
        if (!isset($this->messages)) {
            throw new LogicException('Messages has not been set');
        }

        return $this->messages->map(function (MessageId $messageId) {
            return $messageId->value();
        });
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

    public function isDeleted(): bool
    {
        if (!isset($this->deletedAt)) {
            throw new LogicException('The deleted at must be set');
        }

        return $this->deletedAt->value() !== null;
    }

    public function updateName(string $name, bool $isBulkUpdate = false): self
    {
        $this->setName($name);

        if (!$isBulkUpdate) {
            $this->performUpdate();

            // TODO generate event
        }

        return $this;
    }

    public function updateDescription(string $description, bool $isBulkUpdate): self
    {
        $this->setDescription($description);

        if (!$isBulkUpdate) {
            $this->performUpdate();

            // TODO generate event
        }

        return $this;
    }

    public function addMember(string $newMember): void
    {
        if (!isset($this->members)) {
            throw new LogicException('Chat room\'s member variable is not set');
        }

        if ($this->hasMember($newMember)) {
            throw new UserAlreadyInChatRoomException();
        }

        $this->members->append(UserId::create($newMember));

        // TODO generate event
    }

    public function removeMember(string $member): void
    {
        if (!isset($this->members)) {
            throw new LogicException('Chat room\'s member variable is not set');
        }

        $success = $this->members->delete($member);

        if (!$success) {
            throw new UserDoesNotPertainsToChatRoomException();
        }

        // TODO generate event
    }

    public function addMessage(string $newMessage): void
    {
        if ($this->hasMessage($newMessage)) {
            throw new MessageAlreadyInChatException();
        }

        $this->messages->append(MessageId::create($newMessage));

        // TODO generate event
    }

    public function hasMember(string $member): bool
    {
        return $this->members->contains($member) || $this->creatorId->value() === $member;
    }

    public function hasMessage(string $message): bool
    {
        return $this->messages->contains($message);
    }
}
