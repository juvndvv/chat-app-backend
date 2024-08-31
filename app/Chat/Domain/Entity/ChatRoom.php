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
use SplObjectStorage;

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

    public function getName(): string
    {
        return $this->name->value();
    }

    public function getDescription(): string
    {
        return $this->description->value();
    }

    public function getMembers(): array
    {
        $members = $this->members->map(function (UserId $member) {
            return $member->value();
        });

        array_unshift($members, $this->creatorId->value());

        return $members;
    }

    public function getMessages(): array
    {
        return $this->messages->map(function (MessageId $messageId) {
            return $messageId->value();
        });
    }

    public function addMember(string $newMember): void
    {
        if (!isset($this->members)) {
            throw new LogicException('Chat room\'s member variable is not set');
        }

        if ($this->hasMember($newMember) || $this->creatorId->value() === $newMember) {
            throw new UserAlreadyInChatRoomException();
        }

        $this->members->append(UserId::create($newMember));
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

    public function removeMember(string $member): void
    {
        if (!isset($this->members)) {
            throw new LogicException('Chat room\'s member variable is not set');
        }

        $success = $this->members->delete($member);

        if (!$success) {
            throw new UserDoesNotPertainsToChatRoomException();
        }
    }

    public function hasMember(string $member): bool
    {
        return $this->members->contains($member);
    }

    public function hasMessage(string $message): bool
    {
        return $this->messages->contains($message);
    }
}
